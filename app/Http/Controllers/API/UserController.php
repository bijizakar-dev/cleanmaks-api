<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request) {
        try {
            //validate request
            $request->validate([
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('name', $request->email)
                    ->orWhere('email', $request->email)
                    ->first();

            if (!$user) {
                throw new Exception('Email / Username salah, silahkan periksa kembali');
            }

            if ($user->status != '1') {
                throw new Exception('Akun tidak aktif, silahkan hubungi admin');
            }

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]) && !Auth::attempt(['name' => $request->email, 'password' => $request->password])) {
                throw new Exception('Password salah, silahkan periksa kembali');
            }

            //find user by email
            // $credentials = request(['email', 'password']); //get data request

            $m_user = new User;
            $data_user = $m_user->getUserEmployee($user->id);

            //generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            session([
                'authToken' => $tokenResult,
                'employee' => $data_user
            ]);

            $user =  Auth::user();

            //return response
            return ResponseFormatter::success([
                'status' => true,
                'token' => $tokenResult,
                'msg' => 'Authentication successfully',
                'data' => $data_user
            ], 'Authentication successful');

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ]);
        }
    }

    public function register(Request $request) {
        try {
            //Validate Request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
                'employee_id' => ['required', 'int', 'unique:users']
            ]);

            //create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $request->employee_id
            ]);

            //generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $data_user = User::with('employee')->find($user->id);

            //return response
            return ResponseFormatter::success([
                'status' => true,
                'token' => $tokenResult,
                'msg' => 'User created successfully',
                'data' => $data_user,
            ], 'Registration successful');

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 400);
        }
    }

    public function logout(Request $request) {
        //revoke token
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Logout successful');
    }

    public function fetch(Request $request) {
        //get user
        $user = $request->user();

        $m_user = new User;
        $data_user = $m_user->getUserEmployee($user->id);

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Auth Fetch successful',
            'data' => $data_user
        ], 'Auth Fetch successful');

    }
}
