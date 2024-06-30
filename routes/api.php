<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
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
Route::get('/logout',[LoginController::class,'logout'])->middleware('auth:sanctum');
Route::post('login',[LoginController::class, 'login']);
Route::post('validarCodigo/{id}',[LoginController::class, 'validarCodigo'])->name('validarCodigo')->middleware('signed');

//USUARIOS
Route::post('registrar',[UserController::class, 'create']);
Route::get('roles',[UserController::class, 'roles']);
Route::get('usuarios',[UserController::class, 'index']);
Route::get('usuarios/{id}',[UserController::class, 'show']);
Route::put('usuarios/{id}',[UserController::class, 'update']);
Route::delete('usuarios/{id}',[UserController::class, 'delete']);

//CLIENTES
Route::get('clientes',[ClienteController::class, 'index']);
Route::get('cliente/{id}',[ClienteController::class, 'show']);
Route::post('clientes',[ClienteController::class, 'create']);
Route::put('clientes/{id}',[ClienteController::class, 'update']);
Route::delete('clientes/{id}',[ClienteController::class, 'delete']);

//SUCURSALES
Route::get('sucursales',[SucursalController::class, 'index']);
Route::get('sucursales/{id}',[SucursalController::class, 'show']);
Route::post('sucursales',[SucursalController::class, 'create']);
Route::put('sucursales/{id}',[SucursalController::class, 'update']);
Route::delete('sucursales/{id}',[SucursalController::class, 'delete']);

//PRODUCTOS
Route::get('productos',[ProductoController::class, 'index']);
Route::get('productos/{id}',[ProductoController::class, 'show']);
Route::post('productos',[ProductoController::class, 'create']);
Route::put('productos/{id}',[ProductoController::class, 'update']);
Route::delete('productos/{id}',[ProductoController::class, 'delete']);


Route::get('prueba',[LoginController::class, 'prueba']);
