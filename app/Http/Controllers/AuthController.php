<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cookie;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $roleName = Role::where('id', $user->role_id)->value('role_name');

            $tokenData = [
                'role_name' => $roleName,
                'user_id' => $user->id,
                'created_at' => now()->toDateTimeString(),
            ];
            $token = base64_encode(json_encode($tokenData));

            // Ping Redis to check if it's connected
            try {
                if (Redis::ping()) {
                    Redis::setex('user_token:' . $user->id, 1800, $token);
                    Cookie::queue('user_token', $token, 30);

                    return redirect()->route('transaksi')->with('success', 'Login berhasil!');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Tidak dapat terhubung ke Redis: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Login Gagal! Username atau kata sandi salah');
        }
    }


    public function checkToken(Request $request)
    {
        // Get token from client cookie
        $token = $request->cookie('user_token');

        if ($token) {
            $tokenData = json_decode(base64_decode($token), true);
            $user = Auth::user();

            if ($user && isset($tokenData['user_id'], $tokenData['role_name'])) {
                // Check if the token stored in Redis matches the client token
                $storedToken = Redis::get('user_token:' . $user->id);

                if ($storedToken === $token && $tokenData['user_id'] == $user->id && $tokenData['role_name'] == $user->role->name) {
                    return response()->json([
                        'user_id' => $user->id,
                        'role_name' => $user->role->name,
                    ]);
                }
            }
        }

        return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            // Delete token from Redis
            Redis::del('user_token:' . $user->id);
            // Delete cookie
            Cookie::queue(Cookie::forget('user_token'));

            Auth::logout();
        }

        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }

    public function showChangePasswordForm()
    {
        return view('auth.ubahkatasandi'); // Pastikan path ini sesuai dengan lokasi view Anda
    }

    public function changePassword(Request $request)
    {
        try {
            // Ambil token dari cookie dan decode
            $token = $request->cookie('user_token');
            $tokenData = json_decode(base64_decode($token), true);

            // Validasi apakah token berisi 'user_id'
            if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
                return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
            }

            // Validasi input kata sandi
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Cari user berdasarkan user_id dari token
            $user = User::findOrFail($tokenData['user_id']);

            // Periksa apakah kata sandi lama cocok
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Kata sandi lama tidak sesuai.');
            }

            // Update kolom password, updated_at, dan updated_by
            $user->password = Hash::make($request->new_password);
            $user->updated_at = now();
            $user->updated_by = $tokenData['user_id'];
            $user->save();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Kata sandi berhasil diubah. Silakan login kembali.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan. Silakan login kembali.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah kata sandi. Silakan coba lagi.')->withInput();
        }
    }
}
