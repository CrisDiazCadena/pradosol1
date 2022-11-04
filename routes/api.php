<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AutenticationController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Api\LoginController;

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


Route::post('register',[AutenticationController::class, 'register']);
Route::withoutMiddleware(ValidateJsonApiDocument::class)
    ->post('api/v1/login',LoginController::class)
    ->name('api.v1.login');



Route::group(['middleware' => ['auth:sanctum']], function(){
    //Route::post('logout',[AutenticationController::class, 'logout'])->middleware('employerole');
    Route::post('logout',[AutenticationController::class, 'logout']);
    Route::get('admin/users', [AdminController::class, 'index']);
    Route::get('admin/users/{id}', [AdminController::class, 'show']);
    Route::put('admin/users/updateinfo/{user}', [AdminController::class, 'update'])->middleware('adminrole');
    Route::delete('admin/users/deleteuser/{user}', [AdminController::class, 'destroy'])->middleware('adminrole');
});

Route::get('users', [UserController::class, 'index'])->name('api.v1.users.index');
Route::get('users/{user}', [UserController::class, 'show'])->name('api.v1.users.show');
Route::post('users', [UserController::class, 'create'])->name('api.v1.users.create');



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
