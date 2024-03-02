<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
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
    public function getUserRequest()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de solicitudes.');
            } else {
                $requestData = UserRequest::with('user')->paginate(3);
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

    public function getMyUserRequest($id)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->id != $id) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de solicitudes.');
            } else {
                $requestData = UserRequest::where('user_id', $user->id)->paginate(10);
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

    public function createUserRequest(UserRequestRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->administrator) {
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
