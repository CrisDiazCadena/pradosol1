<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\UpdateSuperUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Administrator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends ApiController
{

    public function getAdmin(Request $request)
    {
        try {

            $user = Auth::user();
            $query = User::whereHas('administrator')->with('administrator', 'typeIdentification', 'validationStatusUser');

            // Verificar si el usuario está autenticado
            if (!$user || !$user->administrator) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos de administrador');
            }

            if ($request->has('order_by') && $request->has('order_by_column')) {
                $orderBy = $request->input('order_by');
                $orderByColumn = $request->input('order_by_column');
                $query->orderBy($orderByColumn, $orderBy);
            }
            if ($request->has('search') && !$request->has('search_column')) {
                $searchTerm = $request->input('search');
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                        ->orWhere('lastname', 'like', "%$searchTerm%")
                        ->orWhere('identification_card', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%")
                        ->orWhere('address', 'like', "%$searchTerm%")
                        ->orWhere('phone', 'like', "%$searchTerm%");
                });
            }
            if ($request->has('search') && $request->has('search_column')) {
                $searchTerm = $request->input('search');
                $searchColumn = $request->input('search_column');
                $query->where($searchColumn, 'like', "%$searchTerm%");
            }

            $adminUsers = $query->paginate(10);

            return $this->successResponse($adminUsers, 200, 'Datos de administradores extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los usuarios: ' . $e->getMessage());
        }
    }

    public function createAdmin(AdminRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden agregar un rol de administrador al usuario.');
            } else if ($user->id == $request->user_id) {
                return $this->errorResponse(403, 'El administrador no puede asignarse roles a si mismo');
            } else {

                DB::beginTransaction();
                if ($request->exist_user == 0) {
                    try {
                        $existingAdminRole = User::find($request->user_id)->roles()->where('role_id', 1)->exists();
                        if ($existingAdminRole) {
                            DB::rollBack();
                            return $this->errorResponse(400, 'El usuario ya ha sido asignado como administrador');
                        } else {
                            $administrator =  new Administrator();
                            $administrator->user_id = $request->user_id;
                            $administrator->save();

                            $role = User::findOrFail($request->user_id);
                            $role->roles()->attach(1);

                            DB::commit();

                            return $this->successResponse($administrator, 201, 'El usuario ha sido añadido como administrador');
                        }
                    } catch (\Exception $e) {
                        // En caso de error, deshacer la transacción
                        DB::rollBack();
                        throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                    }
                }

                if ($request->exist_user == 1) {
                    try {

                        $administrator = new User();
                        $administrator->name = $request->name;
                        $administrator->lastname = $request->lastname;
                        $administrator->identification_card = $request->identification_card;
                        $administrator->address = $request->address;
                        $administrator->password = bcrypt($request->password);
                        $administrator->email = $request->email;
                        $administrator->type_identification_id = $request->type_identification_id;
                        $administrator->verification_token = User::createVerificationToken();

                        if ($request->has('phone') && $administrator->phone != $request->phone) {
                            $administrator->phone = $request->phone;
                        }

                        if ($request->has('status') && $administrator->status != $request->status) {
                            $administrator->status = $request->status;
                        }

                        if ($request->has('verified') && $administrator->verified != $request->verified) {
                            $administrator->verified = $request->verified;
                        }

                        if ($request->has('validation_status_id') && $administrator->validation_status_id != $request->validation_status_id) {
                            $administrator->validation_status_id = $request->validation_status_id;
                        }

                        $administrator->save();

                        $administrator->roles()->attach(1);

                        $userId = $administrator->id;

                        // Crear un nuevo registro de Administrator asociado al usuario recién creado
                        $admin = new Administrator();
                        $admin->user_id = $userId;

                        $admin->save();

                        DB::commit();
                        return $this->successResponse($administrator, 201, 'El administrador ha sido añadido');
                    } catch (\Exception $e) {
                        // En caso de error, deshacer la transacción
                        DB::rollBack();
                        throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                    }
                }

                if (!$request->exist_user || $request->exist_user > 1 || $request->exist_user < 0) {
                    DB::rollBack();
                    $errorMessage = "El contenido esta incompleto";
                    return $this->errorResponse(404, $errorMessage);
                }
            }
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
                return $this->errorResponse(400, $errorMessage);
            }
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al registrar el usuario' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            $emailErrorMessage = isset($errors['email']) ? $errors['email'][1] : 'El correo electrónico ya se ha registrado';
            return $this->errorResponse(404, $emailErrorMessage);
        }
    }


    public function updateAdmin(UpdateSuperUserRequest $request, $id)
    {
        try {
            $AuthenticatedUser = Auth::user();
            if ($AuthenticatedUser->id == $id) {
                return $this->errorResponse(403, 'No puede actualizar sus propios datos. Realice una solicitud para que otro administrador actualice sus datos.');
            }
            if (!$AuthenticatedUser || !$AuthenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden actualizar los usuarios.');
            }
            DB::beginTransaction();
            $user = User::findOrFail($id);

            if ($request->has('name') && $user->name != $request->name) {
                $user->name = $request->name;
            }
            if ($request->has('lastname') && $user->lastname != $request->lastname) {
                $user->lastname = $request->lastname;
            }
            if ($request->has('identification_card') && $user->identification_card != $request->identification_card) {
                $user->identification_card = $request->identification_card;
            }
            if ($request->has('email') && $user->email != $request->email) {
                $user->email = $request->email;
            }
            if ($request->has('type_identification_id') && $user->type_identification_id != $request->type_identification_id) {
                $user->type_identification_id = $request->type_identification_id;
            }

            if ($request->has('status') && $user->status != $request->status) {
                $user->status = $request->status;
            }
            if ($request->has('verified') && $user->verified != $request->verified) {
                $user->verified = $request->verified;
            }
            if ($request->has('validation_status_id') && $user->validation_status_id != $request->validation_status_id) {
                $user->validation_status_id = $request->validation_status_id;
            }
            if ($request->has('address') && $user->address != $request->address) {
                $user->address = $request->address;
            }
            if ($request->has('phone') && $user->phone != $request->phone) {
                $user->phone = $request->phone;
            }

            if (!$user->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato del usuario";
                DB::rollBack();

                return $this->errorResponse(422, $errorMessage);
            }

            $user->save();

            DB::commit();
            $successMessage = "Los datos del usuario se han actualizado satisfactoriamente";
            return $this->successResponse($user, 202, $successMessage);
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack();

            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack();

            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al actualizar el usuario' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function getUsers()
    {
        try {
            $authenticatedUser = Auth::user();
            if (!$authenticatedUser || !$authenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden ver la información de los usuarios.');
            }
            $user = User::all();
            $successMessage = "Los datos del usuario se han extraido satisfactoriamente";
            return $this->successResponse($user, 200, $successMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al extraer los datos' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $id)
    {
        return response()->json([
            'res' => true,
            'user' => $id
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $user->update($request->all());
        return response()->json([
            'res' => true,
            'msg' => 'paciente actualizado',
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'res' => true,
            'msg' => 'paciente eliminado',

        ], 200);
    }
}
