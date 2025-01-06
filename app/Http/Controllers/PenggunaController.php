<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }
        // Mendapatkan query pencarian dari input
        $search = $request->input('search');

        $users = User::with('role')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%")
                    ->orWhereHas('role', function ($q) use ($search) {
                        $q->where('role_name', 'like', "%$search%");
                    });
            })
            ->orderBy('name', 'asc')
            ->paginate(10); // 10 item per halaman

        return view('pengguna.index', compact('users', 'search', 'roleName'));
    }

    public function create()
    {
        $roles = Role::all(); // Ambil semua data peran
        return view('pengguna.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:24|unique:user_accounts,username',
            'role_id' => 'required|numeric|exists:roles,id',
            'phone_number' => 'required|numeric',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username tidak boleh lebih dari 24 karakter.',
            'username.unique' => 'Username sudah digunakan, pilih yang lain.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.numeric' => 'Role harus berupa angka.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            // Hash password menggunakan bcrypt dari username
            $hashedPassword = Hash::make($request->input('username'));

            User::create([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'password' => $hashedPassword, // Simpan password yang sudah di-hash
                'role_id' => $request->input('role_id'),
                'phone_number' => $request->input('phone_number'),
                'created_at' => now(),
                'created_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data pengguna berhasil ditambahkan', 'redirectRoute' => route('pengguna')]);
        } catch (\Exception $e) {
            return back()->with('modal-error', 'Terjadi kesalahan pada server.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('pengguna.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:24|unique:user_accounts,username,' . $id,
            'role_id' => 'required|numeric|exists:roles,id',
            'phone_number' => 'required|numeric',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 100 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username tidak boleh lebih dari 24 karakter.',
            'username.unique' => 'Username sudah digunakan, pilih yang lain.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.numeric' => 'Role harus berupa angka.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            // Update data pada tabel User berdasarkan ID
            User::where('id', $id)->update([
                'name' => $request->input('name'),
                'username' => $request->input('username'),
                'role_id' => $request->input('role_id'),
                'phone_number' => $request->input('phone_number'),
                'updated_at' => Carbon::now(),
                'updated_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data pengguna berhasil diperbarui', 'redirectRoute' => route('pengguna')]);
        } catch (\Exception $e) {
            return back()->with('modal-error', 'Terjadi kesalahan pada server.');
        }
    }


    public function delete($id, Request $request)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $user = User::find($id);
        if ($user) {
            $user->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => $tokenData['user_id']
            ]);
            return redirect()->route('pengguna')->with('success', 'Data pengguna Berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }

    public function resetUserPassword(Request $request, $id)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            $user = User::findOrFail($id);
            $newPassword = Hash::make($user->username);
            $user->update([
                'password' => $newPassword,
                'updated_at' => Carbon::now(),
                'updated_by' => $tokenData['user_id'],
            ]);

            return redirect()->route('pengguna')->with('success', 'Password berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server');
        }
    }
}
