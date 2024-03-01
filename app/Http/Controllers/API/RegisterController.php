<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends ApiController
{
    //
    public function register(RegisterRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->type_identification_id = $request->type_identification_id;
        $user->identification_card = $request->identification_card;
        $user->validation_status_id = $request->validation_status_id;
        $user->save();

        return response()->json([
            'res' => true,
            'msg' => 'Usuario registrado correctamente'
        ],200);



    }

}
