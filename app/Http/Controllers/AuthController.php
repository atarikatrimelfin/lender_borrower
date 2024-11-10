<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create($request->all());

        return $request->ajax() || $request->wantsJson()
        ? $this->apiResponseSuccess($user)
        : redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function reg()
    {
        return view('auth.register');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        return $request->ajax() || $request->wantsJson()
        ? $this->apiResponseSuccess(['token' => $token])
        : redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        return $request->ajax() || $request->wantsJson()
        ? $this->apiResponseSuccess(['message' => 'Successfully logged out'])
        : redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
