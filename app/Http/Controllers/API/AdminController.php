<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\StatusBeneficiaryRequest;
use App\Http\Requests\StatusLicenseRequest;
use App\Http\Requests\UpdateAdminPartnerRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UpdateSuperUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Administrator;
use App\Models\Beneficiary;
use App\Models\Employee;
use App\Models\EmployeeLicense;
use App\Models\Partner;
use App\Models\Products;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends ApiController
{

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

    public function getAllUser(Request $request)
    {
        try {

            $user = Auth::user();
            $query = User::with('roles', 'typeIdentification', 'validationStatusUser');

            // Verificar si el usuario está autenticado
            if (!$user || !$user->administrator) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos de administrador');
            }

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
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

            $columns = collect($request->only(['column_filter_0', 'column_filter_1', 'column_filter_2']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1', 'filter_2']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    if ($column === 'roles') {
                        $query->whereHas('roles', function ($query) use ($filter) {
                            $query->where('id', '=',  $filter);
                        });
                    } else {
                        $query->where("users." . $column, '=',  $filter);
                    }
                }
            });


            $adminUsers = $query->paginate(10);

            return $this->successResponse($adminUsers, 200, 'Datos de usuarios extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los usuarios: ' . $e->getMessage());
        }
    }

    public function getAdmin(Request $request)
    {
        try {

            $user = Auth::user();
            $query = User::whereHas('administrator')->with('administrator', 'typeIdentification', 'validationStatusUser');

            // Verificar si el usuario está autenticado
            if (!$user || !$user->administrator) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos de administrador');
            }

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
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

            $columns = collect($request->only(['column_filter_0', 'column_filter_1']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    $query->where("users." . $column, '=', $filter);
                }
            });

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

    public function statusBeneficiary(StatusBeneficiaryRequest $request, $id)
    {
        try {
            $authenticatedUser = Auth::user();
            if ($authenticatedUser->id == $request->user_id) {
                return $this->errorResponse(403, 'No puede actualizar los datos de sus propios beneficiarios. Realice una solicitud para que otro administrador actualice estos datos.');
            }
            if (!$authenticatedUser || !$authenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden actualizar los datos de los beneficiarios.');
            }
            $user = User::findOrFail($request->user_id);
            if ($user->status == User::INACTIVE_STATUS) {
                return $this->errorResponse(403, 'El usuario no esta activo como socio, primero es necesario verificar los datos del socio y aceptar su ingreso al establecimiento.');
            } elseif ($user->validation_status_id == 2 || $user->validation_status_id == 3) {
                return $this->errorResponse(403, 'Los datos suministrados del socio no se han validado, primero es necesario verificar los datos del socio y confirmar que sus datos son validos.');
            } else {
                DB::beginTransaction();
                $beneficiary = Beneficiary::findOrFail($id);
                if ($request->has('verified') && $beneficiary->verified != $request->verified) {
                    $beneficiary->verified = $request->verified;
                }

                if (!$beneficiary->isDirty()) {
                    $errorMessage = "No se ha modificado ningun dato del beneficiario";
                    DB::rollBack();

                    return $this->errorResponse(422, $errorMessage);
                }

                $beneficiary->save();
                DB::commit();

                $successMessage = "Beneficiario actualizado";
                return $this->successResponse($user, 202, $successMessage);
            }
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

    public function updateAdmin(UpdateSuperUserRequest $request, $id)
    {
        try {
            $authenticatedUser = Auth::user();
            if ($authenticatedUser->id == $id) {
                return $this->errorResponse(403, 'No puede actualizar sus propios datos. Realice una solicitud para que otro administrador actualice sus datos.');
            }
            if (!$authenticatedUser || !$authenticatedUser->administrator) {
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

    public function getEmployee(Request $request)
    {
        try {

            $user = Auth::user();
            $query = User::whereHas('employee')->with('employee', 'typeIdentification', 'validationStatusUser');

            // Verificar si el usuario está autenticado
            if (!$user || !$user->administrator) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos de administrador');
            }

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
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

            $columns = collect($request->only(['column_filter_0', 'column_filter_1']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    $query->where("users." . $column, '=', $filter);
                }
            });

            $employeeUsers = $query->paginate(10);

            return $this->successResponse($employeeUsers, 200, 'Datos de empleados extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los usuarios: ' . $e->getMessage());
        }
    }

    public function createEmployee(EmployeeRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden agregar un rol de empleado al usuario.');
            } else if ($user->id == $request->user_id) {
                return $this->errorResponse(403, 'El administrador no puede asignarse roles a si mismo');
            } else {

                DB::beginTransaction();

                if ($request->exist_user == 0) {
                    try {
                        $existingAdminRole = User::find($request->user_id)->roles()->where('role_id', 2)->exists();
                        if ($existingAdminRole) {
                            DB::rollBack();
                            return $this->errorResponse(400, 'El usuario ya ha sido asignado como empleado');
                        } else {
                            $employee =  new Employee();
                            $employee->position = $request->position;
                            $employee->start_work = $request->start_work;
                            $employee->time_out = $request->time_out;
                            $employee->time_in = $request->time_in;
                            $employee->user_id = $request->user_id;
                            $employee->save();

                            $role = User::findOrFail($request->user_id);
                            $role->roles()->attach(2);

                            DB::commit();

                            return $this->successResponse($employee, 201, 'El usuario ha sido añadido como empleado');
                        }
                    } catch (\Exception $e) {
                        // En caso de error, deshacer la transacción
                        DB::rollBack();
                        throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                    }
                }

                if ($request->exist_user == 1) {
                    try {

                        $employee = new User();
                        $employee->name = $request->name;
                        $employee->lastname = $request->lastname;
                        $employee->identification_card = $request->identification_card;
                        $employee->address = $request->address;
                        $employee->password = bcrypt($request->password);
                        $employee->email = $request->email;
                        $employee->type_identification_id = $request->type_identification_id;
                        $employee->verification_token = User::createVerificationToken();

                        if ($request->has('phone') && $employee->phone != $request->phone) {
                            $employee->phone = $request->phone;
                        }

                        if ($request->has('status') && $employee->status != $request->status) {
                            $employee->status = $request->status;
                        }

                        if ($request->has('verified') && $employee->verified != $request->verified) {
                            $employee->verified = $request->verified;
                        }

                        if ($request->has('validation_status_id') && $employee->validation_status_id != $request->validation_status_id) {
                            $employee->validation_status_id = $request->validation_status_id;
                        }

                        $employee->save();

                        $employee->roles()->attach(2);
                        $userId = $employee->id;

                        // Crear un nuevo registro de empleado asociado al usuario recién creado
                        $worker = new Employee();
                        $worker->user_id = $userId;
                        $worker->position = $request->position;
                        $worker->start_work = $request->start_work;
                        $worker->time_out = $request->time_out;
                        $worker->time_in = $request->time_in;

                        $worker->save();

                        DB::commit();
                        return $this->successResponse($employee, 201, 'El empleado ha sido añadido');
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

    public function updateEmployee(UpdateEmployeeRequest $request, $id)
    {
        try {
            $AuthenticatedUser = Auth::user();
            if ($AuthenticatedUser->id == $id) {
                return $this->errorResponse(403, 'No puede actualizar sus propios datos. Realice una solicitud para que otro administrador actualice sus datos.');
            }
            if (!$AuthenticatedUser || !$AuthenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden actualizar los empleados.');
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
            if ($request->has('position') && $user->employee->position != $request->position) {
                $user->employee->position = $request->position;
            }
            if ($request->has('start_work') && $user->employee->start_work != $request->start_work) {
                $user->employee->start_work = $request->start_work;
            }
            if ($request->has('time_in') && $user->employee->time_in != $request->time_in) {
                $user->employee->time_in = $request->time_in;
            }
            if ($request->has('time_out') && $user->employee->time_out != $request->time_out) {
                $user->employee->time_out = $request->time_out;
            }

            if (!$user->isDirty() && !$user->employee->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato del empleado";
                DB::rollBack();
                return $this->errorResponse(422, $errorMessage);
            }

            if ($user->isDirty()) {
                $user->save();
            }

            if ($user->employee->isDirty()) {
                $user->employee->save();
            }


            DB::commit();
            $successMessage = "Los datos del empleado se han actualizado satisfactoriamente";
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
            $errorMessage = 'Error al actualizar el empleado' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function updateProduct(ProductRequest $request, $id)
    {

        try {
            $authenticatedUser = Auth::user();
            if(!$authenticatedUser->administrator){
                return $this->errorResponse(403, 'No tienes permiso para Actualizar el producto.');
            }
            DB::beginTransaction();
            $product = Products::FindOrFail($id);

            if ($request->has('stock') && $product->stock != $request->stock) {
                $product->stock = $request->stock;
            }

            if ($request->has('name') && $product->name != $request->name) {
                $product->name = $request->name;
            }

            if ($request->has('type') && $product->type != $request->type) {
                $product->type = $request->type;
            }

            if ($request->has('image') && $product->image != $request->image) {
                $product->image = $request->image;
            }

            if ($request->has('status') && $product->status != $request->status) {
                $product->status = $request->status;
            }
            if ($request->has('description') && $product->description != $request->description) {
                $product->description = $request->description;
            }
            if ($request->has('discount_price') && $product->discount_price != $request->discount_price) {
                $product->discount_price = $request->discount_price;
            }

            if (!$product->isDirty()) {
                $errorMessage = "No se ha modificado ningun campo del producto";
                DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
                return $this->errorResponse(422, $errorMessage);
            }

            $product->save();
            DB::commit();
            $message = "Los datos del producto se han actualizado correctamente";
            return $this->successResponse($product, 200, $message);
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS

            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS

            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al obtener el producto' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS

        }
    }

    public function createProduct(ProductRequest $request){
        try{
            $user = Auth::user();
            if(!$user  || !$user->administrator){
                return $this->errorResponse(403, 'Solo los administradores pueden agregar un producto o servicio.');
            }
            else{
                DB::beginTransaction();
                $product = new Products();
                $product->image = $request->image;
                $product->name = $request->name;
                $product->type = $request->type;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->status = $request->status;
                $product->description = $request->description;

                if($request->has('discount_price')){
                    $product->discount_price = $request->discount_price;
                }else{
                    $product->discount_price = null;
                }

                $product->save();
                DB::commit();
                return $this->successResponse($product, 201, 'El producto se ha añadido satisfactoriamente');
            }
        }catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack();

            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack();

            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al crear el producto/servicio ' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function updatePartner(UpdateAdminPartnerRequest $request, $id)
    {
        try {
            $AuthenticatedUser = Auth::user();
            if ($AuthenticatedUser->id == $id) {
                return $this->errorResponse(403, 'No puede actualizar sus propios datos. Realice una solicitud para que otro administrador actualice sus datos.');
            }
            if (!$AuthenticatedUser || !$AuthenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden actualizar los socios.');
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
            if ($request->has('bonding') && $user->partner->bonding != $request->bonding) {
                $user->partner->bonding = $request->bonding;
            }
            if ($request->has('pass') && $user->partner->pass != $request->pass) {
                $user->partner->pass = $request->pass;
            }
            if ($request->has('children') && $user->partner->children != $request->children) {
                $user->partner->children = $request->children;
            }
            if ($request->has('marital_status') && $user->partner->marital_status != $request->marital_status) {
                $user->partner->marital_status = $request->marital_status;
            }

            if (!$user->isDirty() && !$user->partner->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato del socio";
                DB::rollBack();
                return $this->errorResponse(422, $errorMessage);
            }

            if ($user->isDirty()) {
                $user->save();
            }

            if ($user->partner->isDirty()) {
                $user->partner->save();
            }


            DB::commit();
            $successMessage = "Los datos del socio se han actualizado satisfactoriamente";
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
            $errorMessage = 'Error al actualizar el socio' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function createPartner(PartnerRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden agregar un rol de empleado al usuario.');
            } else if ($user->id == $request->user_id) {
                return $this->errorResponse(403, 'El administrador no puede asignarse roles a si mismo');
            } else {

                DB::beginTransaction();

                if ($request->exist_user == 0) {
                    try {
                        $existingAdminRole = User::find($request->user_id)->roles()->where('role_id', 3)->exists();
                        if ($existingAdminRole) {
                            DB::rollBack();
                            return $this->errorResponse(400, 'El usuario ya ha sido asignado como Socio');
                        } else {
                            $partner =  new Partner();
                            $partner->bonding = $request->bonding;
                            $partner->pass = $request->pass;
                            $partner->children = $request->children;
                            $partner->marital_status = $request->marital_status;
                            $partner->user_id = $request->user_id;

                            $partner->save();

                            $role = User::findOrFail($request->user_id);

                            if ($request->has('validation_status_id') && $role->validation_status_id != $request->validation_status_id) {
                                $role->validation_status_id = $request->validation_status_id;
                                $role->save();
                            }
                            $role->roles()->attach(3);

                            DB::commit();

                            return $this->successResponse($partner, 201, 'El usuario ha sido añadido como Socio');
                        }
                    } catch (\Exception $e) {
                        // En caso de error, deshacer la transacción
                        DB::rollBack();
                        throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                    }
                }

                if ($request->exist_user == 1) {
                    try {

                        $partner = new User();
                        $partner->name = $request->name;
                        $partner->lastname = $request->lastname;
                        $partner->identification_card = $request->identification_card;
                        $partner->address = $request->address;
                        $partner->password = bcrypt($request->password);
                        $partner->email = $request->email;
                        $partner->type_identification_id = $request->type_identification_id;
                        $partner->verification_token = User::createVerificationToken();

                        if ($request->has('phone') && $partner->phone != $request->phone) {
                            $partner->phone = $request->phone;
                        }

                        if ($request->has('status') && $partner->status != $request->status) {
                            $partner->status = $request->status;
                        }

                        if ($request->has('verified') && $partner->verified != $request->verified) {
                            $partner->verified = $request->verified;
                        }

                        if ($request->has('validation_status_id') && $partner->validation_status_id != $request->validation_status_id) {
                            $partner->validation_status_id = $request->validation_status_id;
                        }

                        $partner->save();

                        $partner->roles()->attach(3);
                        $userId = $partner->id;

                        // Crear un nuevo registro de empleado asociado al usuario recién creado


                        $partnerRole = new Partner();
                        $partnerRole->user_id = $userId;
                        $partnerRole->bonding = $request->bonding;
                        $partnerRole->pass = $request->pass;
                        $partnerRole->children = $request->children;
                        $partnerRole->marital_status = $request->marital_status;

                        $partnerRole->save();

                        DB::commit();
                        return $this->successResponse($partner, 201, 'El Socio ha sido añadido');
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

    public function getLicense(Request $request)
    {
        try {

            $user = Auth::user();
            $query = EmployeeLicense::query()->with('employee.user', "typeLicense");

            // Verificar si el usuario está autenticado
            if (!$user || !$user->administrator) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos de administrador');
            }

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
                $orderBy = $request->input('order_by');
                $orderByColumn = $request->input('order_by_column');

                if ($orderByColumn === 'user') {
                    // Ordenar por el nombre del usuario
                    $query->join('employees', 'employee_licenses.employee_id', '=', 'employees.id')
                        ->join('users', 'employees.user_id', '=', 'users.id')
                        ->select('employee_licenses.*', 'users.name as user_name', 'users.lastname as user_lastname') // Seleccionar las columnas necesarias de ambas tablas
                        ->orderBy('users.name', $orderBy);
                } else {
                    $query->orderBy($orderByColumn, $orderBy);
                }
            }
            if ($request->has('search') && !$request->has('search_column')) {
                $searchTerm = $request->input('search');
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('description', 'like', "%$searchTerm%")
                        ->orWhere('comments', 'like', "%$searchTerm%")
                        ->orWhere('start_license', 'like', "%$searchTerm%")
                        ->orWhere('end_license', 'like', "%$searchTerm%")
                        ->orWhereHas('employee.user', function ($query) use ($searchTerm) {
                            $query->where('name', 'like', "%$searchTerm%")
                                ->orWhere('lastname', 'like', "%$searchTerm%")
                                ->orWhere('identification_card', 'like', "%$searchTerm%");
                        });
                });
            }

            if ($request->has('user_search') && $request->has('search_column')) {
                $userSearchTerm = $request->input('user_search');
                $searchColumn = $request->input('search_column');
                $query->whereHas('employee.user', function ($query) use ($userSearchTerm) {
                    $query->where('name', 'like', "%$userSearchTerm%")
                        ->orWhere('lastname', 'like', "%$userSearchTerm%")
                        ->orWhere('identification_card', 'like', "%$userSearchTerm%");
                });
            }

            if ($request->has('search') && $request->has('search_column')) {
                $searchTerm = $request->input('search');
                $searchColumn = $request->input('search_column');
                $query->where($searchColumn, 'like', "%$searchTerm%");
            }

            $columns = collect($request->only(['column_filter_0', 'column_filter_1']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    $query->where("employee_licenses." . $column, '=', $filter);
                }
            });

            $employeeLicenses = $query->paginate(10);

            return $this->successResponse($employeeLicenses, 200, 'Datos de licencias extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener las licencias: ' . $e->getMessage());
        }
    }

    public function statusLicense(StatusLicenseRequest $request, $id)
    {
        try {
            $authenticatedUser = Auth::user();
            if ($authenticatedUser->id == $request->user_id) {
                return $this->errorResponse(403, 'No puede actualizar los datos de sus propias licencias. Realice una solicitud para que otro administrador actualice estos datos.');
            }
            if (!$authenticatedUser || !$authenticatedUser->administrator) {
                return $this->errorResponse(403, 'Solo los administradores pueden actualizar los datos de las licencias de los empleados.');
            }
            DB::beginTransaction();
            $license = EmployeeLicense::findOrFail($id);
            if ($request->has('verified') && $license->verified != $request->verified) {
                $license->verified = $request->verified;
            }

            if ($request->has('comments') && $license->comments != $request->comments) {
                $license->comments = $request->comments;
            }

            if (!$license->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato de la licencia solicitada";
                DB::rollBack();

                return $this->errorResponse(422, $errorMessage);
            }

            $license->save();
            DB::commit();

            $successMessage = "Licencia actualizada";
            return $this->successResponse($license, 202, $successMessage);
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack();

            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack();

            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al actualizar la licencia ' . $e->getMessage();
            DB::rollBack();
            return $this->errorResponse(400, $errorMessage);
        }
    }
}
