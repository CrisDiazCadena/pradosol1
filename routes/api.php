<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PartnerController;
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


Route::post('register',[RegisterController::class, 'register'])->name('register');
Route::withoutMiddleware(ValidateJsonApiDocument::class)->post('login',LoginController::class)->name('login');



/*Route::group(['middleware' => ['auth:sanctum']], function(){
    //Route::post('logout',[AutenticationController::class, 'logout'])->middleware('employerole');
    Route::post('logout',[LoginController::class, 'logout'])->name('logout');
    Route::get('admin/users', [AdminController::class, 'index']);
    Route::get('admin/users/{id}', [AdminController::class, 'show']);
    Route::put('admin/users/updateinfo/{user}', [AdminController::class, 'update'])->middleware('adminrole');
    Route::delete('admin/users/deleteuser/{user}', [AdminController::class, 'destroy'])->middleware('adminrole');
});*/

Route::get('users', [UserController::class, 'index'])->name('api.v1.users.index');
Route::get('users/{user}', [UserController::class, 'show'])->name('api.v1.users.show');
Route::post('users/create', [UserController::class, 'create'])->name('api.v1.users.create');
Route::patch('users/update', [UserController::class, 'update'])->name('api.v1.users.update');
Route::get('users/{filter}', [UserController::class, 'filter'])->name('api.v1.users.filter');

Route::get('{users}', [PartnerController::class, 'show'])->name('api.v1.partners.show');
Route::patch('{users}/update', [PartnerController::class, 'update'])->name('api.v1.partners.update');

Route::post('users/beneficiary', [BeneficiaryController::class, 'create'])->name('api.v1.beneficiary.create');
Route::get('{users}/{beneficiary}', [BeneficiaryController::class, 'show'])->name('api.v1.beneficiary.show');
Route::patch('{users}/update/{beneficiary}', [BeneficiaryController::class, 'update'])->name('api.v1.beneficiary.update');

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
