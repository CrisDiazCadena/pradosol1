<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntranceTicketRequest;
use App\Models\entranceTicket;
use App\Models\Partner;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntranceTicketController extends ApiController
{

    public function addTickets(EntranceTicketRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user->employee) {
                return $this->errorResponse(403, 'No tienes permiso para registrar el beneficiario.');
            } else {
                $partner = Partner::findOrFail($request->partner_id);
                if ($partner->pass <= 0) {
                    return $this->errorResponse(422, 'No tiene tiquetes de ingreso.');
                } else {
                    try {

                        DB::beginTransaction();

                        $ticket = new entranceTicket();
                        $ticket->name = $request->name;
                        $ticket->identification_card = $request->identification_card;
                        $ticket->type_identification_id = $request->type_identification_id;
                        $ticket->partner_id = $request->partner_id;
                        $partner->pass = $partner->pass - 1;
                        $ticket->save();
                        $partner->save();

                        DB::commit();

                        return $this->successResponse($ticket, 201, 'El pase del socio se ha agregado exitosamente');
                    } catch (\Exception $e) {
                        // En caso de error, deshacer la transacción
                        DB::rollBack();
                        throw $e; // Relanzar la excepción para manejarla fuera de la transacción
                    }
                }
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
}
