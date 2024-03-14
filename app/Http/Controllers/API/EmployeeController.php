<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\EmployeeLicense;
use App\Models\entranceTicket;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMyOwnData($id)
    {

        try {
            $user = Auth::user();
            if ($user->id != $id) {
                return $this->errorResponse(403, 'No tienes permiso para observar los datos del empleado.');
            } else {
                // Verifica si el usuario tiene un asociado Employee
                if ($user->employee) {
                    $message = "Los datos del empleado se han extraido correctamente";
                    $employeeData = $user->employee;
                    $licensesData = EmployeeLicense::where('employee_id', $user->employee->id)->paginate(2);
                    $licensesData->load('typeLicense');
                    $responseData = [
                        'employee' => $employeeData,
                        'licenses' => $licensesData
                    ];
                    return $this->successResponse($responseData, 200, $message);
                } else {
                    $message = "El usuario solicitado no esta asociado como empleado";
                    return $this->errorResponse(404, $message);
                }
            }
        } catch (AuthenticationException $e) {
            $message = "Token no válido o no proporcionado";
            return $this->errorResponse(401, $message);
        }
    }

    public function showBeneficiary($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de los beneficiario.');
            } else if  ($user->employee || $user->administrator) {
                $beneficiariesData = Beneficiary::where('partner_id', $id)->with('typeIdentification')->get();
                return $this->successResponse($beneficiariesData, 200, 'Datos de beneficiarios extraidos correctamente');
            }else{
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de los beneficiario.');
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
        }
    }


    public function showTicket($id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->employee) {
                return $this->errorResponse(403, 'No tienes permiso ver la informacion de los tiquetes del socio.');
            } else {
                $ticketData = entranceTicket::where('partner_id', $id)->with('typeIdentification')->orderBy("created_at", "desc")->paginate(5);

                return $this->successResponse($ticketData, 200, 'Datos de tiquetes extraidos correctamente');
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

    public function destroy($id)
    {
        //
    }
}
