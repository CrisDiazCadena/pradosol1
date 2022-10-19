<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AutenticationController extends Controller
{
    //
    public function register(RegisterRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->type_identification = $request->type_identification;
        $user->identification_card = $request->identification_card;
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'res' => true,
            'msg' => 'Usuario registrado correctamente'
        ],200);



    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)){
        throw ValidationException::withMessages([
        'msg' => ['Las credenciales proveidas no son correctas!.'],
        ]);
    }

    if ($request->type != $user->type){
        throw ValidationException::withMessages([
        'msg' => ['Las credenciales proveidas no son correctas!.'],
        ]);
    }

    $token = $user->createToken($request->email)->plainTextToken;

    return response()->json([
        'res' => true,
        'msg' => 'Acceso concedido',
        'token' => $token,
        'user' => $user->type
    ],200);

    }

    public function logout(Request $request){
        $request -> user()-> currentAccessToken()->delete();
        return response()->json([
            'res' => true,
            'msg' => 'LogOut satisfactorio',

        ],200);
    }

}
