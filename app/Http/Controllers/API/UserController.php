<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user){
        return UserResource::make($user);
    }

    public function index(): UserCollection
    {
        return UserCollection::make(User::all());
    }

    public function create(Request $request)
    {
        $user = User::create([
            'type_identification' => $request->input('data.users.type_identification'),
            'identification_card'  => $request->input('data.users.identification_card'),
            'password' => $request->input('data.users.password'),
            'name' => $request->input('data.users.name'),
            'lastname' => $request->input('data.users.lastname'),
            'status' => $request->input('data.users.status'),
            'address' => $request->input('data.users.address'),
            'email' => $request->input('data.users.email'),
            'phone' => $request->input('data.users.phone')
        ]);
        return response()->json($user,201);
    }
}

