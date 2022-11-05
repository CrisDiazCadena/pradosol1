<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{

    public function __construct()
    {
        $this->middleware('sociorole'); 
    }

    public function create(Request $request)
    {
        $user = Beneficiary::create([
            'name' => $request->input('data.users.name'),
            'lastname' => $request->input('data.users.lastname'),
            'type_identification' => $request->input('data.users.type_identification'),
            'identification_card'  => $request->input('data.users.identification_card'),
        ]);
        return response()->json($user,201);
    }

    public function show(Beneficiary $user){
        return UserResource::make($user);
    }

    public function update(UpdateUserRequest $request, Beneficiary $beneficiary)
    {

        $beneficiary->update($request->validated());
        return response()->json([
            'res' => true,
            'msg' => 'beneficiario actualizado',
            'user' =>$beneficiary
        ], 200);
    }

}
