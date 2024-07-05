<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Cliente\ClienteRequest;
use Illuminate\Database\QueryException;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::select('id','nombre', 'correo', 'telefono')->where('estatus', true)->with('sucursales');
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $clientes->count();
        $clientes->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
        $clientes = $clientes->get();
            return response()->json([
                    'total' => $total,
                    'page' => $page,
                    'perPage' => $perPage,
                    'firstItem' => $firstItem,
                    'lastItem' => $lastItem,
                    'data' => $clientes
                ],200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener las sucursales",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function clientes()
    {
        $clientes = Cliente::select('id','nombre', 'correo', 'telefono')->where('estatus', true)->with('sucursales');
        try {
        $clientes = $clientes->get();
            return response()->json($clientes,200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener los clientes",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(ClienteRequest $request)
    {
        $validatedData = $request->validated();
        
        try {
            $cliente = Cliente::create([
                'nombre' => $validatedData['nombre'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
                'estatus' => true
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear el cliente."
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
            'msg' => 'Cliente creado con éxito',
            'cliente' => $cliente->id
        ], 200);  
    }

    public function show($id){
        $cliente = Cliente::select('id','nombre', 'correo', 'telefono')->with('sucursales')->find($id);
        if(!$cliente){
            return response()->json([
                'msg' => 'Cliente no encontrado',
            ], 404);
        }
        return response()->json($cliente, 200);
    }



    public function update(ClienteRequest $request, $id){
        $validatedData = $request->validated();

        $cliente = Cliente::find($id);
        if(!$cliente){
            return response()->json([
                'msg' => 'Cliente no encontrado',
            ], 404);
        }
        try {
            $cliente->update([
                'nombre' => $validatedData['nombre'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al editar el cliente."
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
            'msg' => 'Cliente editado con éxito',
            'cliente' => $cliente->id
        ], 200);  
    }

    public function delete($id){
        $cliente = Cliente::find($id);
        if(!$cliente){
            return response()->json([
                'msg' => 'Cliente no encontrado',
            ], 404);
        }
        try {
            $cliente->update([
                'estatus' => false
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar el cliente."
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
            'msg' => 'Cliente eliminado con éxito'
        ], 200);  
    }

}
