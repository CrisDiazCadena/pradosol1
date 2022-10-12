<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::post('Login', function(){
    $credentials = request()->validate(['email' => ['required', 'email']]);
    $remember = dd(request()->filled('remember'));
    if (Auth::attempt($credentials, $remember)){
        request()->session()->regenerate();
        return redirect()->intended(' zz')
    };
    throw ValidationException::withMessages([
        'email' => __('auth.failed')
    ])

 });*/
 


//proteccion de rutas 
//ruta definida -> middleware('auth'):

//redireccion si esta autenticado
//ruta definida -> middleware('guest')