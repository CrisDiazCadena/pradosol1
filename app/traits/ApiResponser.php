<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    protected function successResponse($data, $code, $message)
    {
        $response = [
            'code' => 0,
            'message' => $message,
            'error' => null,
            'data' => $data
        ];
        return response()->json($response, $code);
    }

    protected function loginResponse($data, $code, $message, $token)
    {
        $response = [
            'code' => 0,
            'message' => $message,
            'error' => null,
            'data' => $data,
            'token' => $token
        ];
        return response()->json($response, $code);
    }

    protected function errorResponse($code, $message)
    {
        $response = [
            'code' => -2,
            'message' => $message,
            'error' => 'Error' . $code,
            'data' => null
        ];
        return response()->json($response, $code);
    }

    protected function showAll(Collection $collection, $code = 200, $message){
        $response = [
            'code' => 0,
            'message' => $message,
            'error' => null,
            'data' => $collection
        ];
        return response()->json($response, $code);
    }

    protected function showOne(Model $instance, $code = 200, $message){
        $response = [
            'code' => 0,
            'message' => $message,
            'error' => null,
            'data' => $instance
        ];
        return response()->json($response, $code);
    }
}
