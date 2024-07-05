<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Producto\ProductoRequest;
use Illuminate\Database\QueryException;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::select('id','nombre','descripcion','precio','img')->where('estatus', true);
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $productos->count();
        $productos->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
        $productos = $productos->get();
            return response()->json([
                    'total' => $total,
                    'page' => $page,
                    'perPage' => $perPage,
                    'firstItem' => $firstItem,
                    'lastItem' => $lastItem,
                    'data' => $productos
                ],200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener los productos",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function productos()
    {
        $productos = Producto::select('id','nombre','descripcion','precio','img')->where('estatus', true);

        try {
        $productos = $productos->get();
            return response()->json($productos,200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener los productos",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(ProductoRequest $request)
    {
        $validatedData = $request->validated();
        
        try {
            $Producto = Producto::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'precio' => $validatedData['precio'],
                'estatus' => true
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear el Producto."
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
            'msg' => 'Producto creado con éxito',
            'Producto' => $Producto->id
        ], 200);  
    }

    public function show($id){
        $Producto = Producto::select('id','nombre','descripcion','precio','img')->find($id);
        if(!$Producto){
            return response()->json([
                'msg' => 'Producto no encontrado',
            ], 404);
        }
        return response()->json($Producto, 200);
    }



    public function update(ProductoRequest $request, $id){
        $validatedData = $request->validated();
        $img =

        $Producto = Producto::find($id);
        if(!$Producto){
            return response()->json([
                'msg' => 'Producto no encontrado',
            ], 404);
        }
        try {
            $Producto->update([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'precio' => $validatedData['precio'],
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al editar el Producto."
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
            'msg' => 'Producto editado con éxito',
            'Producto' => $Producto->id
        ], 200);  
    }

    public function delete($id){
        $Producto = Producto::find($id);
        if(!$Producto){
            return response()->json([
                'msg' => 'Producto no encontrado',
            ], 404);
        }
        try {
            $Producto->update([
                'estatus' => false
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar el Producto."
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
            'msg' => 'Producto eliminado con éxito'
        ], 200);  
    }}
