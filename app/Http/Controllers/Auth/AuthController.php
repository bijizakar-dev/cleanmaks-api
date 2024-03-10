<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function v_login() {
        //check if user already logged
        $user = Auth::user();

        if(!empty($user)) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function handleLogin(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']); //get data request

        if(!Auth::attempt($credentials)) {
            return redirect('login')
                ->withInput()
                ->withErrors(['login_failed'=>'Password / Email salah silahkah periksa kembali']);
        }

        $user = User::where('email', $credentials['email'])->first();
        if(!Hash::check($request->password, $user->password)) {
            return redirect('login')
                ->withInput()
                ->withErrors(['login_failed'=>'Password / Email salah silahkah periksa kembali']);
        }

        $m_user = new User;
        $data_user = $m_user->getUserEmployee($user->id);

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        session([
            'authToken' => $tokenResult,
            'employee' => $data_user
        ]);

        $user =  Auth::user();

        //return
        return redirect()->intended('/');
    }

    public function v_register() {
        $user = Auth::user();

        if($user) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function logout() {
        Auth::logout();
        Session::flush();

        return redirect('login');
    }
}
