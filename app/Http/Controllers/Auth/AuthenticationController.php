<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login() { return view('auth.login'); }

    public function register() { return view('auth.register'); }

    public function login_submit(Request $request) {
        $request->validate([
            'email'=>'required|email|exists:users,email',
            'password'=>'required|min:3'
        ],[
            'email'=>[
                'required'=>'Email is required',
                'email'=>"Email should be a valid email address",
                'exists'=>'Email does not match our records'
            ],
            'password'=>[
                'required'=>'Password is required',
                'min'=>'Password must contain minimum :min letters'
            ]
        ],$request->all());

        $remember = $request->remember == 'on' ? true : false;
        if(Auth::attempt($request->only("email","password"),$remember)){
            $user = Auth::user();
            $request->session()->regenerate();
            if($user->is_admin){
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('user.dashboard');
            }
        }else{
            return redirect()->back()->withErrors(["password"=>"Invalid password"]);
        }
    }

    public function register_submit(Request $request) {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'phone'=>'required|numeric',
            'password'=>'required|confirmed|min:3'
        ],[
            'name'=>[
                'required'=>'Name is required'
            ],
            'email'=>[
                'required'=>'Email is required',
                'email'=>"Email should be a valid email address",
                'exists'=>'Email does not match our records'
            ],
            'phone'=>[
                'required'=>'Phone Number is required',
                'numeric'=>'Phone Number must be a valid phone number'
            ],
            'password'=>[
                'required'=>'Password is required',
                'confirmed'=>'Password must macth with confirm password',
                'min'=>'Password must contain minimum :min letters'
            ]
        ],$request->all());



        $insert_data = [
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
            'is_admin'=>0
        ];
        
        $user = User::create($insert_data);
        Auth::login($user);
        return redirect()->route("user.dashboard")->with("message",["msg"=>"Account created successfully"]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

