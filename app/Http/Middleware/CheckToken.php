<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get token from client cookie
        $token = $request->cookie('user_token');

        if ($token) {
            // Decode token yang diterima dari cookie
            $tokenData = json_decode(base64_decode($token), true);

            // Ambil user yang sedang login
            $user = Auth::user();

            if ($user && isset($tokenData['user_id'], $tokenData['role_name'])) {
                // Ambil token yang tersimpan di Redis
                $storedToken = Redis::get('user_token:' . $user->id);

                // Validasi token dan data pengguna
                if (
                    $storedToken === $token &&
                    $tokenData['user_id'] == $user->id &&
                    $tokenData['role_name'] == $user->role->role_name
                ) {

                    // Token valid, lanjutkan permintaan
                    return $next($request);
                }
            }
        }

        // If token is invalid, redirect to login with error message
        return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
    }
}
