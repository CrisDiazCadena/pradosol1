<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Requests\UpdateUserRequestRequest;
use App\Http\Requests\UserRequestRequest;
use App\Models\UserRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRequestController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserRequest(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de solicitudes.');
            } else {
                $query = UserRequest::with('user.typeIdentification');
                if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
                    $orderBy = $request->input('order_by');
                    $orderByColumn = $request->input('order_by_column');

                    if ($orderByColumn === 'user') {
                        // Ordenar por el nombre del usuario
                        $query->join('users', 'user_requests.user_id', '=', 'users.id')
                            ->select('user_requests.*', 'users.name as user_name', 'users.lastname as user_lastname') // Seleccionar las columnas necesarias de ambas tablas
                            ->orderBy('users.name', $orderBy);
                    } else {
                        $query->orderBy($orderByColumn, $orderBy);
                    }
                }

                if ($request->has('search') && !$request->has('search_column')) {
                    $searchTerm = $request->input('search');
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('type', 'like', "%$searchTerm%")
                            ->orWhere('title', 'like', "%$searchTerm%")
                            ->orWhere('description', 'like', "%$searchTerm%")
                            ->orWhereHas('user', function ($query) use ($searchTerm) {
                                $query->where('name', 'like', "%$searchTerm%")
                                    ->orWhere('lastname', 'like', "%$searchTerm%")
                                    ->orWhere('identification_card', 'like', "%$searchTerm%")
                                    ->orWhere('email', 'like', "%$searchTerm%");
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
                    $query->whereHas('user', function ($query) use ($userSearchTerm, $searchColumn) {
                        $query->where($searchColumn, 'like', "%$userSearchTerm%");
                    });
                }

                $columns = collect($request->only(['column_filter_0', 'column_filter_1']))->values();
                $filters = collect($request->only(['filter_0', 'filter_1']))->values();

                // Aplicar los filtros a la consulta
                $query->where(function ($query) use ($columns, $filters) {
                    $count = min($columns->count(), $filters->count());
                    for ($i = 0; $i < $count; $i++) {
                        $column = $columns[$i];
                        $filter = $filters[$i];
                            $query->where("user_requests." . $column, $filter);
                    }
                });

                $requestData = $query->paginate($request->pages);
                return $this->successResponse($requestData, 200, 'Datos de solicitudes extraidos correctamente');
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al traer contenido de solicitudes ' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function getMyUserRequest($id, Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->id != $id) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de solicitudes.');
            } else {

                $query = UserRequest::where('user_id', $user->id);

                if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
                    $orderBy = $request->input('order_by');
                    $orderByColumn = $request->input('order_by_column');

                    $query->orderBy($orderByColumn, $orderBy);
                }

                if ($request->has('search') && !$request->has('search_column')) {
                    $searchTerm = $request->input('search');
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('type', 'like', "%$searchTerm%")
                            ->orWhere('title', 'like', "%$searchTerm%")
                            ->orWhere('description', 'like', "%$searchTerm%");
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
                            $query->where("user_requests." . $column, '=', $filter);
                    }
                });

                $requestData = $query->paginate($request->pages);
                return $this->successResponse($requestData, 200, 'Datos de solicitudes extraidos correctamente');
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al traer contenido de solicitdes' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    public function updateUserRequest($id, UpdateUserRequestRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'No tienes permiso para Actualizar solicitudes.');
            } else {
                try {

                    $userRequest = UserRequest::findOrFail($id);

                    if ($user->administrator->id === $userRequest->user_id) {
                        return $this->errorResponse(403, 'No tiene permitido contestar las solicitudes formuladas por el mismo usuario.');
                    } else {
                        DB::beginTransaction();
                        if ($request->has('comments') && $userRequest->comments != $request->comments) {
                            $userRequest->comments = $request->comments;
                        }
                        if ($request->has('status') && $userRequest->status != $request->status) {
                            $userRequest->status = $request->status;
                        }
                        if ($request->has('admin_id') && $userRequest->admin_id != $request->admin_id) {
                            $userRequest->admin_id = $request->admin_id;
                        }
                        if (!$userRequest->isDirty()) {
                            $errorMessage = "No se ha modificado ningun dato de la solicitud";
                            return $this->errorResponse(422, $errorMessage);
                        }
                        $userRequest->save();
                        DB::commit();

                        $successMessage = "La solicitud se ha contestado satisfactoriamente";
                        return $this->successResponse($userRequest, 202, $successMessage);
                    }
                } catch (\Exception $e) {
                    // En caso de error, deshacer la transacción
                    DB::rollBack();
                    throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                }
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al responder la solicitud ' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
            //DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
        }
    }


    public function createUserRequest(UserRequestRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse(403, 'No tienes permiso para crear solicitudes.');
            } else {
                try {
                    DB::beginTransaction();

                    $userRequest = new UserRequest();
                    $userRequest->type = $request->type;
                    $userRequest->title = $request->title;
                    $userRequest->description = $request->description;
                    $userRequest->user_id = $request->user_id;
                    $userRequest->save();

                    DB::commit();

                    return $this->successResponse($userRequest, 201, 'La solicitud se ha creado correctamente.');
                } catch (\Exception $e) {
                    // En caso de error, deshacer la transacción
                    DB::rollBack();
                    throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                }
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al traer contenido de solicitdes' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
