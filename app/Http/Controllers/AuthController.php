<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected MockDataService $mockService;

    public function __construct(MockDataService $mockService)
    {
        $this->mockService = $mockService;
        $this->mockService->ensureInitialized();
    }

    public function showLogin()
    {
        return view('auth.login', [
            'users' => $this->mockService->getUsers(),
        ]);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $users = $this->mockService->getUsers();

        foreach ($users as $user) {
            if (strtolower($user['email']) === strtolower($email)) {
                $this->mockService->switchCurrentUser($user['id']);
                return redirect()->route('tasks.index')->with('success', "Selamat datang kembali, {$user['name']}!");
            }
        }

        return redirect()->back()->with('error', 'Email tidak ditemukan dalam data akun.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $result = $this->mockService->registerUser($request->all());

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login dengan email Anda.');
    }

    public function logout()
    {
        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}

