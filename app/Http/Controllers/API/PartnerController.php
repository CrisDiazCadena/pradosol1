<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('clientrole'); 
    }

    public function show(User $user){
        return UserResource::make($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        $user->update($request->validated());
        return response()->json([
            'res' => true,
            'msg' => 'usuario actualizado',
            'user' =>$user
        ], 200);
    }
}
