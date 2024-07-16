<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\watchApp\LoginController as LoginWatch;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PdfController;
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
//RUTAS PARA AUTENTICACION WATCH
Route::post('generateCode/{id}',[LoginWatch::class, 'generateCode'])->name('generateCode');
Route::post('validateCode',[LoginWatch::class, 'validateCode'])->name('validateCode');

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

//VISITAS
Route::get('visitas',[VisitaController::class, 'index']);
Route::get('visitas/{id}',[VisitaController::class, 'show']);
Route::post('visitas',[VisitaController::class, 'create']);
Route::put('visitas/{id}',[VisitaController::class, 'update']);
Route::patch('/visitas/autorizar/{id}', [VisitaController::class, 'autorizar']);
Route::patch('/visitas/cancelar/{id}', [VisitaController::class, 'cancelar']);
Route::patch('/visitas/finalizar/{id}', [VisitaController::class, 'finalizar']);

//ORDENES
Route::get('ordenes',[OrdenController::class, 'index']);
Route::get('ordenes/{id}',[OrdenController::class, 'show']);
Route::post('ordenes',[OrdenController::class, 'create']);
Route::put('ordenes/{id}',[OrdenController::class, 'update']);
Route::patch('/ordenes/autorizar/{id}', [OrdenController::class, 'autorizar']);
Route::patch('/ordenes/finalizar/{id}', [OrdenController::class, 'finalizar']);
Route::patch('/ordenes/cancelar/{id}', [OrdenController::class, 'cancelar']);
Route::get('/ordeneServicios/generarPdf',[OrdenController::class, 'generatePdf']);

//RUTAS SIN FILTRAR
Route::get('productosSinFiltrar',[ProductoController::class, 'productos']);
Route::get('clientesSinFiltrar',[ClienteController::class, 'clientes']);
Route::get('sucursalesSinFiltrar',[SucursalController::class, 'sucursales']);
Route::get('tecnicos',[UserController::class, 'tecnicos']);
Route::get('secretarias',[UserController::class, 'secretaria']);

//agenda
Route::get('agenda',[VisitaController::class, 'agenda']);


Route::get('prueba',[LoginController::class, 'prueba']);
