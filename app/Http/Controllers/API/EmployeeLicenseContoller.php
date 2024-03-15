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

    public function getMyLicenses(Request $request)
    {
        try {

            $user = Auth::user();
            $query = EmployeeLicense::where('employee_id', $user->employee->id)->with('employee.user', "typeLicense");

            // Verificar si el usuario está autenticado
            if (!$user || !$user->employee) {
                return $this->errorResponse(401, 'Usuario no autenticado. No tiene permisos para ver las licencias del empleado');
            }

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
                $orderBy = $request->input('order_by');
                $orderByColumn = $request->input('order_by_column');
                $query->orderBy($orderByColumn, $orderBy);
            }
            if ($request->has('search') && !$request->has('search_column')) {
                $searchTerm = $request->input('search');
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('description', 'like', "%$searchTerm%")
                        ->orWhere('comments', 'like', "%$searchTerm%")
                        ->orWhere('start_license', 'like', "%$searchTerm%")
                        ->orWhere('end_license', 'like', "%$searchTerm%");
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

    public function addLicense(LicenseRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user->employee || $user->employee->id !== $request->employee_id) {
                return $this->errorResponse(403, 'No tienes permiso para registrar el beneficiario.');
            } else {

                $license = new EmployeeLicense();
                if ($request->has('description')) {
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
