<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  
    public function showRegister()
    {
        return view('auth.register');
    }

public function register(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

 
    $role =  'member';
    if(User::count() === 0){
        $role = 'admin';
    }          

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $role,
    ]);
    
    Auth::login($user);

    return redirect('/dashboard');
}


    public function showLogin()
    {
        return view('auth.login');
    }

    
    public function login(Request $request)
    {
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]) ) {
            return redirect('/dashboard');
        }

        return back()->with('error', 'Email or Password incorrect');
    }

   
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}