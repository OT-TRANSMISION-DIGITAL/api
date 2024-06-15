<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

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

//Route::middleware('auth:sanctum')->group(function(){ Route::get('/logout/{id}',[LoginController::class,'logout']);}); //mandar el id del usuario


//RUTAS PARA AUTENTICACION
Route::get('/logout/{id}',[LoginController::class,'logout'])->middleware('auth:sanctum');
Route::post('login',[LoginController::class, 'login']);
Route::post('validarCodigo/{id}',[LoginController::class, 'validarCodigo'])->name('validarCodigo')->middleware('signed');

//USUARIOS
Route::post('registrar',[UserController::class, 'create']);
Route::get('roles',[UserController::class, 'roles']);



Route::get('prueba',[LoginController::class, 'prueba']);
