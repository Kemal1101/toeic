<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if(Auth::check()){ // jika sudah login, maka redirect ke halaman home
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $roleId = $user->role_id;

                // Tentukan URL redirect berdasarkan role_id
                switch ($roleId) {
                    case 1:
                        $redirectUrl = route('dashboard.upa');
                        break;
                    case 2:
                        $redirectUrl = route('dashboard.mahasiswa');
                        break;
                    default:
                        $redirectUrl = route('login'); // fallback
                        break;
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Login berhasil',
                    'redirect' => $redirectUrl
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Username atau password salah'
                ]);
            }
        }

        // Jika bukan AJAX, kembalikan ke login biasa
        return redirect()->route('login');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
