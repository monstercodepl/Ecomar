<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($attributes)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'You are logged in.');
        }

        return back()->withErrors(['email' => 'Email or password invalid.'])->withInput();
    }
    
    public function destroy(Request $request)
    {
        Auth::logout();

        // Unieważnienie sesji i regeneracja tokena
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', "You've been logged out.");
    }
}
