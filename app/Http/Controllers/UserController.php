<?php

namespace App\Http\Controllers;

// Use Controllers
use App\Http\Controllers\Controller;
// Use Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RegisterUpdateRequest;
use App\Http\Requests\Imagen\ImagenRequest;

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
        $usuarios = User::select('id','nombre', 'correo', 'telefono', 'img', 'rol_id')->where('estatus', true)->with('rol');
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
        $user = User::select('id','nombre', 'correo', 'telefono', 'img' , 'rol_id')->with('rol')->find($id);
        if(!$user){
            return response()->json([
                'msg' => 'Usuario no encontrado',
            ], 404);
        }
        return response()->json($user, 200);
    }



    public function update(RegisterUpdateRequest $request, $id){
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

    public function tecnicos()
    {
        $tecnicos = User::select('id','nombre', 'correo', 'telefono')
        ->where('estatus', true)->where('rol_id', 3);


        try {
        $tecnicos = $tecnicos->get();
            return response()->json($tecnicos,200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener los tecnicos",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    //Este metodo es para insertar y actualizar la imagen, si tiene una imagen antes, la borra 
    public function insertImagen(ImagenRequest $request, $id)
    {
        $validatedData = $request->validated();

        $User = User::find($id);

        if (!$User) {
            return response()->json([
                'msg' => 'Usuario no encontrado',
            ], 404);
        }

        $image = $validatedData['img'];
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $originalName = str_replace(' ', '_', $originalName);
        $timestamp = date('YmdHis');
        $extension = $image->getClientOriginalExtension();
        $name = $originalName . $timestamp . '.' . $extension;
        $destinationPath = public_path('/imagenes/usuarios');
        if ($image->move($destinationPath, $name)) {
            $validatedData['img'] = env('URL_USUARIOS') . 'usuarios/' . $name;

            // Eliminar la imagen anterior si existe
            if ($User->img) {
                $oldImagePath = public_path('/imagenes/usuarios/' . basename($User->img));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Error al subir la imagen',
            ], 500);
        }

        try {
            $User->update([
                'img' => $validatedData['img']
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al guardar la imagen del Usuario."
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
            'msg' => 'Imagen guardada con exito',
            'User' => $User->id
        ], 200);
    }

    public function deleteImagen($id)
    {
        $User = User::find($id);

        if (!$User) {
            return response()->json([
                'msg' => 'User no encontrado',
            ], 404);
        }


        if($User->img != null){
        $oldImagePath = public_path('/imagenes/usuarios/' . basename($User->img));
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }            
        }

        try {
            $User->update([
                'img' => null
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar la imagen del Usuario."
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
            'msg' => 'Imagen eliminada con exito',
            'User' => $User->id
        ], 200);
    }

}
