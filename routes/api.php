<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\AutenticationController;
use App\Http\Controllers\API\BeneficiaryController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\EmployeeLicenseContoller;
use App\Http\Controllers\API\EntranceTicketController;
use App\Http\Controllers\API\LicensesTypeController;
use App\Http\Controllers\API\PartnerController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\API\TypeIdentificationUserController;
use App\Http\Controllers\API\UserRequestController;

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


Route::post('register',[AutenticationController::class, 'register'])->name('api.v1.register.create');
Route::post('login',[AutenticationController::class, 'login'])->name('api.v1.login.create');



Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout',[AutenticationController::class, 'logout'])->name('api.v1.login.delete');
    Route::put('user/{id}', [UserController::class, 'updateMyUser'])->name('api.v1.user.update.own.data');

    Route::get('user/request/{id}', [UserRequestController::class, 'getMyUserRequest'])->name('api.v1.user.request.get.own.data');
    Route::get('user/request', [UserRequestController::class, 'getUserRequest'])->name('api.v1.user.request.get.data');
    Route::post('user/request', [UserRequestController::class, 'createUserRequest'])->name('api.v1.user.request.create.data');
    Route::put('user/request/{id}', [UserRequestController::class, 'updateUserRequest'])->name('api.v1.user.request.put.data');

    Route::get('employees/{id}', [EmployeeController::class, 'showMyOwnData'])->name('api.v1.employee.show.own.data');
    Route::get('employees/{id}', [EmployeeController::class, 'showMyOwnData'])->name('api.v1.employee.show.own.data');
    Route::get('employees/beneficiary/{id}', [EmployeeController::class, 'showBeneficiary'])->name('api.v1.employee.show.partner.beneficiary.data');
    Route::get('employees/tickets/{id}', [EmployeeController::class, 'showTicket'])->name('api.v1.employee.show.partner.ticket.data');

    Route::post('employees/licenses',[EmployeeLicenseContoller::class, 'addLicense'])->name('api.v1.employee.license.create.data');
    Route::get('employees/licenses/type',[LicensesTypeController::class, 'index'])->name('api.v1.licensesType.index');

    Route::post('employees/tickets', [EntranceTicketController::class, 'addTickets'])->name('api.v1.add.ticket.partner.data');

    Route::get('partners', [UserController::class, 'getPartners'])->name('api.v1.partner.get.all.data');
    Route::get('partners/{id}', [PartnerController::class, 'showMyOwnData'])->name('api.v1.partner.show.own.data');
    Route::put('partners/{id}', [PartnerController::class, 'updateMyPartner'])->name('api.v1.partner.update.own.data');

    Route::get('beneficiary', [UserController::class, 'getBeneficiaries'])->name('api.v1.beneficiaries.get.all.data');
    Route::post('beneficiary/create', [BeneficiaryController::class, 'addBeneficiary'])->name('api.v1.beneficiary.add.data');
    Route::put('beneficiary/{id}', [BeneficiaryController::class, 'updateMyBeneficiaries'])->name('api.v1.beneficiary.update.data');

});
/*
//Route::post('logout',[AutenticationController::class, 'logout'])->middleware('employerole');
Route::post('logout',[LoginController::class, 'logout'])->name('logout');
Route::get('admin/users', [AdminController::class, 'index']);
Route::get('admin/users/{id}', [AdminController::class, 'show']);
Route::put('admin/users/updateinfo/{user}', [AdminController::class, 'update'])->middleware('adminrole');
Route::delete('admin/users/deleteuser/{user}', [AdminController::class, 'destroy'])->middleware('adminrole');
*/

Route::get('domain/identification',[TypeIdentificationUserController::class, 'index'])->name('api.v1.typeIdentificationUser.index');
//Route::resource('type_identification_users', [UserController::class, 'index'], ['only' =>['index', 'show']]);
Route::get('users/{user}', [UserController::class, 'show'])->name('api.v1.users.show');
Route::post('users/create', [UserController::class, 'create'])->name('api.v1.users.create');
Route::patch('users/update', [UserController::class, 'update'])->name('api.v1.users.update');
Route::get('users/{filter}', [UserController::class, 'filter'])->name('api.v1.users.filter');
Route::get('{users}', [PartnerController::class, 'show'])->name('api.v1.partners.show');
Route::patch('{users}/update', [PartnerController::class, 'update'])->name('api.v1.partners.update');

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
