<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\GreetingMail;
use App\Models\RegisteredUser;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:registered_users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Insert data into the registered_users table
        RegisteredUser::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
       

        return redirect()->route('dashboard')->with('success', 'User registered successfully.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Redirect to dashboard
        return redirect()->intended('/dashboard');
    }

    // Redirect back with an error message if login fails
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
