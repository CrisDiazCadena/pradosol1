<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
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
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'res' => true,
            'msg' => 'Usuario registrado correctamente'
        ],200);



    }

}