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


    public function index(Request $request)
    {
        $usuarios = User::select('id','nombre', 'correo', 'telefono', 'rol_id')->where('estatus', true)->with('rol');
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $usuarios->count();
        $usuarios->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
        $usuarios = $usuarios->get();
            return response()->json([
                    'total' => $total,
                    'page' => $page,
                    'perPage' => $perPage,
                    'firstItem' => $firstItem,
                    'lastItem' => $lastItem,
                    'data' => $usuarios
                ],200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener los usuarios",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show($id){
        $user = User::select('id','nombre', 'correo', 'telefono', 'rol_id')->with('rol')->find($id);
        if(!$user){
            return response()->json([
                'msg' => 'Usuario no encontrado',
            ], 404);
        }
        return response()->json($user, 200);
    }



    public function update(RegisterRequest $request, $id){
        $validatedData = $request->validated();

        $User = User::find($id);
        if(!$User){
            return response()->json([
                'msg' => 'User no encontrado',
            ], 404);
        }
        try {
            $User->update([
                'nombre' => $validatedData['nombre'],
                'correo' => $validatedData['correo'],
                'telefono' => $validatedData['telefono'],
                'rol_id' => $validatedData['rol_id'],
                'password' => Hash::make($validatedData['password']),
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al editar el User."
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
            'msg' => 'User editado con éxito',
            'User' => $User->id
        ], 200);  
    }

    public function delete($id){
        $User = User::find($id);
        if(!$User){
            return response()->json([
                'msg' => 'User no encontrado',
            ], 404);
        }
        try {
            $User->update([
                'estatus' => false
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar el User."
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
            'msg' => 'User eliminado con éxito'
        ], 200);  
    }

}
