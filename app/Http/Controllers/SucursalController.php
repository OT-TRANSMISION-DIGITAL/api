<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use App\Models\Cliente;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Cliente\ClienteRequest;
use App\Http\Requests\Sucursal\SucursalRequest;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $sucursales = Sucursal::select('id','nombre', 'direccion', 'telefono','cliente_id')
        ->where('estatus', true)->with('cliente');
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $sucursales->count();
        $sucursales->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
        $sucursales = $sucursales->get();
            return response()->json([
                    'total' => $total,
                    'page' => $page,
                    'perPage' => $perPage,
                    'firstItem' => $firstItem,
                    'lastItem' => $lastItem,
                    'data' => $sucursales
                ],200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener las sucursales",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function sucursales()
    {
        $sucursales = Sucursal::select('id','nombre', 'direccion', 'telefono','cliente_id')
        ->where('estatus', true)->with('cliente');

        try {
        $sucursales = $sucursales->get();
            return response()->json($sucursales,200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener las sucursales",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(SucursalRequest $request)
    {
        $validatedData = $request->validated();
        
        try {
            $sucursal = Sucursal::create([
                'nombre' => $validatedData['nombre'],
                'direccion' => $validatedData['direccion'],
                'telefono' => $validatedData['telefono'],
                'cliente_id' => $validatedData['cliente_id'],
                'estatus' => true
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear la sucursal."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Sucursal creada con éxito',
            'sucursal' => $sucursal->id
        ], 200);  
    }

    public function show($id){
        $sucursal = Sucursal::select('id','nombre', 'direccion', 'telefono','cliente_id')
        ->with('cliente')->find($id);
        if(!$sucursal){
            return response()->json([
                'msg' => 'Sucursal no encontrada',
            ], 404);
        }
        return response()->json($sucursal, 200);
    }



    public function update(SucursalRequest $request, $id){
        $validatedData = $request->validated();

        $sucursal = Sucursal::find($id);
        if(!$sucursal){
            return response()->json([
                'msg' => 'Sucursal no encontrado',
            ], 404);
        }
        try {
            $sucursal->update([
                'nombre' => $validatedData['nombre'],
                'direccion' => $validatedData['direccion'],
                'telefono' => $validatedData['telefono'],
                'cliente_id' => $validatedData['cliente_id'],
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear la sucursal."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Sucursal editada con éxito',
            'Sucursal' => $sucursal->id
        ], 200);  
    }

    public function delete($id){
        $sucursal = Sucursal::find($id);
        if(!$sucursal){
            return response()->json([
                'msg' => 'Sucursal no encontrada',
            ], 404);
        }
        try {
            $sucursal->update([
                'estatus' => false
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar la sucursal."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Sucursal eliminada con éxito'
        ], 200);  
    }
}
