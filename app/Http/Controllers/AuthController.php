<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function showlogin(){
        return view('auth.login');
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=> 'required'
        ]);

        if(Auth::attempt($request->only('email','password'))){
            return redirect()->route('work.index');
        }
        return back()->withErrors([
            'email'=> 'Invalid credentials'
        ]);
    }

    public function showregister(){
        return view('auth.register');
    }

    public function register(Request $request){
        $request->validate([
            'username'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        return redirect()->route('login')->with('success', 'Account created successfully!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    public function index()
    {
        return view('workstatus.index');
    }
}
