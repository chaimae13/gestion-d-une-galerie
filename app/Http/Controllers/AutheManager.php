<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AutheManager extends Controller
{
    function login() {
        return view('Auth/login');
     }
     function register() {
        return view('Auth/register');
     }
     function home() {
        $user = auth()->user();
        return view(('welcome'), compact('user'));
     }

     function loginPost(Request $request) {
        $request->validate([
            'email'=> 'required',
            'password' =>  'required'
        ]);
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            return redirect()->intended(route('welcome'));
        }else
        return redirect(route('login'))->with("error","login details not valid");
        
        
     }
     function registerPost(Request $request) {
        $request->validate([
            'name'=>  'required',
            'email'=> 'required|email|unique:users',
            'password' =>  'required'
        ]);
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);
        $user= User::create($data);
        if(!$user){
            return redirect(route('register'))->with("error","registration failed");
        }else
        return redirect(route('login'))->with("success","registration successful");
    }

        function logout(){
            Session::flush();
            Auth::logout();
            return redirect()->intended(route('login'));

        }


}
