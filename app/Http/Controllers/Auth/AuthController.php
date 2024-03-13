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

        $user = User::where('name', $request->email)
                    ->orWhere('email', $request->email)
                    ->first();

        if (!$user) {
            return redirect('login')
                ->withInput()
                ->withErrors(['login_failed' => 'Email / Username salah, silahkan periksa kembali']);
        }

        if ($user->status != '1') {
            return redirect('login')
                ->withInput()
                ->withErrors(['login_failed' => 'Akun tidak aktif, silahkan hubungi admin']);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]) && !Auth::attempt(['name' => $request->email, 'password' => $request->password])) {
            return redirect('login')
                ->withInput()
                ->withErrors(['login_failed' => 'Password salah, silahkan periksa kembali']);
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
