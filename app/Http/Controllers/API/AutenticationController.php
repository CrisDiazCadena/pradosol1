<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Partner;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AutenticationController extends ApiController
{
    //
    public function register(RegisterRequest $request)
    {

        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->password = bcrypt($request->password);
            $user->email = $request->email;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->type_identification_id = $request->type_identification_id;
            $user->identification_card = $request->identification_card;
            $user->verification_token = User::createVerificationToken();
            $user->save();

            $user->roles()->attach(3);

            $partner = $user->partner()->create([
                'bonding' => Partner::NO_BONDING,
                'pass' => 0,
                'children' => 0,
                'marital_status' => Partner::SINGLE_STATUS,
            ]);

            DB::commit();
            return $this->successResponse($user, 201, 'Usuario Registrado Satisfactoriamente');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];

            if ($errorCode == 1062) { // 1062 es el código de error para una violación de clave única
                // Detectar si es una violación de clave única para el correo o el documento
                if (str_contains($e->getMessage(), 'users.email_unique')) {
                    $errorMessage = 'El correo electrónico ya está registrado.';
                } elseif (str_contains($e->getMessage(), 'users.users_identification_card_unique')) {
                    $errorMessage = 'El documento de identificación ya está registrado.';
                } else {
                    $errorMessage = 'Error de duplicación: ' . $e->getMessage();
                }
                DB::rollBack();
                return $this->errorResponse(400, $errorMessage);
            }
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack();
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al registrar el usuario' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->toArray();
            $emailErrorMessage = isset($errors['email']) ? $errors['email'][1] : 'El correo electrónico ya se ha registrado';
            return $this->errorResponse(404, $emailErrorMessage);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->with('roles', 'typeIdentification', 'validationStatusUser')->first();

            if (!$user) {
                $errorMessage = "El correo electrónico no está registrado";
                return $this->errorResponse(401, $errorMessage);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {

                $errorMessage = "La contraseña es incorrecta";
                return $this->errorResponse(401, $errorMessage);
            }
            $token = $user->createToken($request->email)->plainTextToken;
            $successMessage = "Acceso concedido";
            return $this->loginResponse($user, 200, $successMessage, $token);
        } catch (\Exception $e) {
            $errorMessage = 'Error al iniciar sesión' . $e->getMessage();
            return $this->errorResponse(500, $errorMessage);
            //DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'res' => true,
            'msg' => 'Sesion eliminada',

        ], 200);
    }
}
