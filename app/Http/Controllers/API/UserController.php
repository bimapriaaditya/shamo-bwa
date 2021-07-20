<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string', new Password],
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'mesasge' => 'Error !!!',
                'token_type' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Cek Data / Validasi data
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            // cek Autentikasi
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return ResponseFormatter::error([
                    'message' => 'Unautorized'
                ], "Authentication Failed", 500);
            }

            // Bila Autentikasi berhasil ambil data user dari db
            $user = User::where('email', $request->email)->first();

            // Doubel check, cek password secara manual
            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid Cradentials');
            }

            // Buat Result Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Unautorized'
            ], "Authentication Failed");
        }
    }
}