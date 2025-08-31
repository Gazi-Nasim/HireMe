<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ], 200);
    }


    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Log out the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*******  cbe03482-4ed9-4d66-b157-b58c89310bd5  *******/
    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Add other necessary methods and logic for authentication
}
