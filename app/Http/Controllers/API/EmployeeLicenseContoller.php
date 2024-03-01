<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Models\EmployeeLicense;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeLicenseContoller extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addLicense(LicenseRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user->employee || $user->employee->id !== $request->employee_id) {
                return $this->errorResponse(403, 'No tienes permiso para registrar el beneficiario.');
            } else {

                $license = new EmployeeLicense();
                if($request->has('description')){
                    $license->description = $request->description;
                }
                $license->support = $request->support;
                $license->start_license = $request->start_license;
                $license->end_license = $request->end_license;
                $license->type_license_id = $request->type_license_id;
                $license->employee_id = $request->employee_id;

                $license->save();
                $license->load('typeLicense');

                return $this->successResponse($license, 201, 'La licencia se ha registrado y guardado en el sistema');
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Contenido no encontrado";
            return $this->errorResponse(404, $errorMessage);
        } catch (\Exception $e) {
            $errorMessage = 'Error al registrar la licencia' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
            //DB::rollBack(); // Deshace la transacción - DESCOMENTAR CUANDO COMIENCEN PRUEBAS
        }
    }
}
