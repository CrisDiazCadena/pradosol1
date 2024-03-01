<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\BeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Beneficiary;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends ApiController
{

    public function addBeneficiary(BeneficiaryRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user->partner || $user->partner->id !== $request->partner_id) {
                return $this->errorResponse(403, 'No tienes permiso para registrar el beneficiario.');
            } else {

                $beneficiary = new Beneficiary();
                $beneficiary->name = $request->name;
                $beneficiary->lastname = $request->lastname;
                $beneficiary->type_beneficiary = $request->type_beneficiary;
                $beneficiary->identification_card = $request->identification_card;
                $beneficiary->partner_id = $request->partner_id;
                $beneficiary->type_identification_id = $request->type_identification_id;
                $beneficiary->save();

                return $this->successResponse($beneficiary, 201, 'Beneficario del socio ha sido registrado exitosamente');
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al registrar el beneficiario' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
            //DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
        }
    }

    public function updateMyBeneficiaries(UpdateBeneficiaryRequest $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user->partner || $user->partner->id !== $request->partner_id) {
                return $this->errorResponse(403, 'No tienes permiso para registrar el beneficiario.');
            } else {
                $beneficiary = Beneficiary::findOrFail($id);
                if ($request->has('name') && $beneficiary->name != $request->name) {
                    $beneficiary->name = $request->name;
                }

                if ($request->has('lastname') && $beneficiary->lastname != $request->lastname) {
                    $beneficiary->lastname = $request->lastname;
                }
                if ($request->has('type_beneficiary') && $beneficiary->type_beneficiary != $request->type_beneficiary) {
                    $beneficiary->type_beneficiary = $request->type_beneficiary;
                }

                if ($request->has('identification_card') && $beneficiary->identification_card != $request->identification_card) {
                    $beneficiary->identification_card = $request->identification_card;
                }
                if ($request->has('type_identification_id') && $beneficiary->type_identification_id != $request->type_identification_id) {
                    $beneficiary->type_identification_id = $request->type_identification_id;
                }
                if (!$beneficiary->isDirty()) {
                    $errorMessage = "No se ha modificado ningun dato del beneficiario";
                    return $this->errorResponse(422, $errorMessage);
                }
                $beneficiary->verified = "false";
                $beneficiary->save();
                $successMessage = "Los datos del socio se han actualizado satisfactoriamente";
                return $this->successResponse($beneficiary, 202, $successMessage);
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al actualizar el beneficiario' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
            //DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
        }
    }
}
