<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\updatePartnerRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Beneficiary;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerController extends ApiController
{

    public function showMyOwnData($id)
    {

        try {
            $user = Auth::user();
            if ($user->id != $id) {
                return $this->errorResponse(403, 'No tienes permiso para observar los datos del socio.');
            } else {
                // Verifica si el usuario tiene un asociado Partner
                if ($user->partner) {
                    $message = "Los datos del socio se han extraido correctamente";
                    $partnerData = $user->partner;
                    $beneficiariesData = Beneficiary::where('partner_id', $user->partner->id)->paginate(2);
                    $responseData = [
                        'partner' => $partnerData,
                        'beneficiaries' => $beneficiariesData
                    ];
                    return $this->successResponse($responseData, 200, $message);
                } else {
                    $message = "El usuario solicitado no esta asociado como socio";
                    return $this->errorResponse(404, $message);
                }
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        }
    }

    public function updateMyPartner(updatePartnerRequest $request, $id)
    {
        try {
            $AuthenticatedUser = Auth::user();
            if ($AuthenticatedUser->id != $request->user_id) {
                return $this->errorResponse(403, 'No tienes permiso para Actualizar el usuario.');
            }

            DB::beginTransaction();
            $partner = Partner::findOrFail($id);

            if ($request->has('children') && $partner->children != $request->children) {
                $partner->children = $request->children;
            }

            if ($request->has('marital_status') && $partner->marital_status != $request->marital_status) {
                $partner->marital_status = $request->marital_status;
            }

            if (!$partner->isDirty()) {
                $errorMessage = "No se ha modificado ningun dato del socio";
                return $this->errorResponse(422, $errorMessage);
            }
            $user = User::findOrFail($request->user_id);
            $user->validation_status_id = 2;
            $user->save();
            $partner->save();
            DB::commit();
            $successMessage = "Los datos del socio se han actualizado satisfactoriamente";
            return $this->successResponse($partner, 202, $successMessage);
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            DB::rollBack();
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            DB::rollBack();
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Error al actualizar el usuario' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }
}
