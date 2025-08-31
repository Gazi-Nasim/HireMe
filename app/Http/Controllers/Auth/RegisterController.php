<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        $validations = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,employer,jobseeker'
        ]);

        if ($validations->fails()) {
            return response()->json([
                'errors' => $validations->errors(),
                'valid_roles' => ['employer', 'jobseeker'],
            ], 422);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already exists'], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        $token = auth()->login($user);
        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
