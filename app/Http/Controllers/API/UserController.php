<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Administrator;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Beneficiary;
use App\Models\Employee;
use App\Models\Partner;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{

    public function show(User $user)
    {
        return UserResource::make($user);
    }


    public function getPartners(Request $request)
    {
        try {

            $user = Auth::user();
            $query = User::whereHas('partner')->with('partner', 'typeIdentification', 'validationStatusUser');

            // Verificar si el usuario está autenticado
            if (!$user) {
                return $this->errorResponse(401, 'Usuario no autenticado');
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

            $columns = collect($request->only(['column_filter_0', 'column_filter_1', 'column_filter_2','column_filter_3', 'column_filter_4']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1', 'filter_2','filter_3', 'filter_4']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    if ($column === 'bonding' || $column === 'marital_status') {
                        $query->whereHas('partner', function ($query) use ($column, $filter) {
                            $query->where($column , '=', $filter);
                        });
                    } else {
                        $query->where("users." . $column, '=',  $filter);
                    }
                }
            });


            $partnersUsers = $query->paginate(10);

            return $this->successResponse($partnersUsers, 200, 'Datos de socios extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los usuarios: ' . $e->getMessage());
        }
    }


    public function getBeneficiaries(Request $request)
    {
        try {

            $user = Auth::user();
            $query = Beneficiary::query()->with('partner.user', 'typeIdentification');

            // Verificar si el usuario está autenticado
            if ($user && ($user->employee || $user->administrator)) {
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
                            ->orWhere('type_beneficiary', 'like', "%$searchTerm%")
                            ->orWhereHas('partner.user', function ($query) use ($searchTerm) {
                                $query->where('name', 'like', "%$searchTerm%")
                                    ->orWhere('lastname', 'like', "%$searchTerm%")
                                    ->orWhere('identification_card', 'like', "%$searchTerm%");
                            });
                    });
                }
                if ($request->has('search') && $request->has('search_column')) {
                    $searchTerm = $request->input('search');
                    $searchColumn = $request->input('search_column');
                    $query->where($searchColumn, 'like', "%$searchTerm%");
                }

                if ($request->has('user_search') && $request->has('search_column')) {
                    $userSearchTerm = $request->input('user_search');
                    $searchColumn = $request->input('search_column');
                    $query->whereHas('partner.user', function ($query) use ($userSearchTerm) {
                        $query->where('name', 'like', "%$userSearchTerm%")
                        ->orWhere('lastname', 'like', "%$userSearchTerm%")
                        ->orWhere('identification_card', 'like', "%$userSearchTerm%");
                    });
                }

                $columns = collect($request->only(['column_filter_0', 'column_filter_1','column_filter_2','column_filter_3','column_filter_4']))->values();
                $filters = collect($request->only(['filter_0', 'filter_1','filter_2','filter_3','filter_4']))->values();

                // Aplicar los filtros a la consulta
                $query->where(function ($query) use ($columns, $filters) {
                    $count = min($columns->count(), $filters->count());
                    for ($i = 0; $i < $count; $i++) {
                        $column = $columns[$i];
                        $filter = $filters[$i];
                        if ($column === 'status' || $column === 'validation_status_id') {
                            $query->whereHas('partner.user', function ($query) use ($column, $filter) {
                                $query->where($column, '=', $filter);
                            });
                        }elseif ($column === 'marital_status' || $column === 'bonding') {
                            $query->whereHas('partner', function ($query) use ($filter, $column) {
                                $query->where( $column, '=',  $filter);
                            });
                        }else{
                            $query->where("beneficiaries." . $column, $filter);
                        }
                    }
                });

                $beneficiaries = $query->paginate(10);

                return $this->successResponse($beneficiaries, 200, 'Datos de beneficiarios extraidos correctamente');
            } else {
                return $this->errorResponse(401, 'Usuario no autenticado');
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los usuarios: ' . $e->getMessage());
        }
    }

    public function index(): UserCollection
    {
        return UserCollection::make(User::paginate());
    }

    public function create(UpdateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('data.users.name'),
            'lastname' => $request->input('data.users.lastname'),
            'email' => $request->input('data.users.email'),
            'password' => $request->input('data.users.password'),
            'type_identification_id' => $request->input('data.users.type_identification_id'),
            'identification_card'  => $request->input('data.users.identification_card'),
            'address' => $request->input('data.users.address'),
            'phone' => $request->input('data.users.phone'),
            'validation_status_id' => $request->input('data.users.validation_status_id'),
        ]);
        return response()->json($user, 201);
    }




    public function updateMyUser(UpdateUserRequest $request, $id)
    {
        try {
            $AuthenticatedUser = Auth::user();
            if ($AuthenticatedUser->id != $id) {
                return $this->errorResponse(403, 'No tienes permiso para Actualizar el usuario.');
            }
            DB::beginTransaction();
            $user = User::findOrFail($id);

            if ($request->has('address') && $user->address != $request->address) {
                $user->address = $request->address;
            }

            if ($request->has('phone') && $user->phone != $request->phone) {
                $user->phone = $request->phone;
            }

            if (!$user->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato del usuario";
                DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
                return $this->errorResponse(422, $errorMessage);
            }

            $user->save();
            DB::commit();
            $successMessage = "Los datos del usuario se han actualizado satisfactoriamente";
            return $this->successResponse($user, 202, $successMessage);
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS

            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS

            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al actualizar el usuario' . $e->getMessage();
            DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function filter(Request $request): UserCollection
    {
        if ($request == 'administrator') {
            return UserCollection::make(User::where(Administrator::select('user_id'), 'id'));
        } else {
            if ($request == 'partner') {
                return UserCollection::make(User::where(Partner::select('user_id'), 'id'));
            } else {
                if ($request == 'employee') {
                    return UserCollection::make(User::where(Employee::select('user_id'), 'id'));
                }
            }
        }
    }
}
