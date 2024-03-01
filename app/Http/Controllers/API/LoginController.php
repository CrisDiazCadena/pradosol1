<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::whereEmail($request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        $plainTextToken = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'plain-text-token' => $plainTextToken
        ]);
    }

    public function logout(Request $request){
        $request -> user()-> currentAccessToken()->delete();
        return response()->json([
            'res' => true,
            'msg' => 'LogOut satisfactorio',

        ],200);
    }
}
