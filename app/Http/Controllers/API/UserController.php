<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Administrator;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employee;
use App\Models\Partner;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('adminrole'); 
    }

    public function show(User $user){
        return UserResource::make($user);
    }

    public function index(): UserCollection
    {
        return UserCollection::make(User::paginate());
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

    public function update(UpdateUserRequest $request, User $user)
    {

        $user->update($request->validated());
        return response()->json([
            'res' => true,
            'msg' => 'paciente actualizado',
            'user' =>$user
        ], 200);
    }

    public function filter(Request $request): UserCollection
    {
        if($request == 'administrator'){
            return UserCollection::make(User::where(Administrator::select('user_id'), 'id'));
        }
        else{
            if($request == 'partner'){
                return UserCollection::make(User::where(Partner::select('user_id'), 'id'));
            }
            else{
                if($request == 'employee'){
                    return UserCollection::make(User::where(Employee::select('user_id'), 'id'));
                }
            }
        }
    }
}

