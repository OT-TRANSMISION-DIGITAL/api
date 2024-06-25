<?php

namespace App\Http\Controllers;

// Use Controllers
use App\Http\Controllers\Controller;
// Use Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
// Use Models
use App\Models\User;
use App\Models\Rol;
// Use Facades
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
// Use Exceptions
use Illuminate\Database\QueryException;
use Exception;

class UserController extends Controller
{
    public function create(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        
        try {
            $user = User::create([
                'nombre' => $validatedData['nombre'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
                'password' => Hash::make($validatedData['password']),
                'rol_id' => $validatedData['rol_id'],
                'estatus' => true
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear el usuario."
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
            'msg' => 'Usuario creado con éxito',
            'usuario' => $user->id
        ], 200);  
    }

    public function roles(Request $request)
    {
        try {
            $roles = Rol::select('id', 'nombre')->get();
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "Error al obtener los roles."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        if($roles->isEmpty()) return response()->json([
            'msg' => 'No se encontraron roles.',
            'roles' => []
        ], 404);

        return response()->json([
            'msg' => 'Roles obtenidos con éxito.',
            'roles' => $roles,
        ], 200);
    }

}
