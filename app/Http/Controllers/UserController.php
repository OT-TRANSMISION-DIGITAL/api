<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|min:3|regex:/^[a-zA-Z]*$/',
                'correo' => 'required|string|email|max:255|unique:users',
                'telefono' => 'required|string|min:10|max:10',
                'password' => 'required|string|min:8',
                'rol_id' => 'integer|required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    "msg"=>"No se cumplieron las validaciones",
                    "errors" => $validator->errors()
                ], 400);
            }

            $user = new User();
            $user->nombre = $request->nombre;
            $user->correo = $request->correo;
            $user->telefono = $request->telefono;
            $user->password = Hash::make($request->password);
            $user->rol_id = $request->rol_id;
            $user->estatus = true;

            if ($user->save()) {
                return response()->json([
                    'msg' => 'Usuario creado con éxito',
                    'usuario' => $user->id
                ], 200);                
            }

        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            //Log::channel('slackerror')->error('LoginController@registro (api) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            //Log::channel('slackerror')->error('LoginController@registro (api) Error PDO', [$e->getMessage()]);
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            //Log::channel('slackerror')->error('LoginController@registro (api) Excepción no controlada', [$e->getMessage()]);
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        }
    }

    public function roles()
    {
        try {
            $roles = DB::table('roles')->select('roles.id','roles.nombre')
            //->where('roles.name', '!=', 'admin')
            ->get();
            if ($roles === null) {
                return abort(403);
            }

            return response()->json([
                'msg' => 'Roles :) ',
                'roles' => $roles,
            ], 200);


        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            //Log::channel('slackerror')->error('LoginController@registro (api) Error consulta SQL', [$e->getMessage()]);
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        } catch (PDOException $e) {
            // Manejo de la excepción de PDO
            Log::error('Error de PDO: ' . $e->getMessage());
            //Log::channel('slackerror')->error('LoginController@registro (api) Error PDO', [$e->getMessage()]);
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            //Log::channel('slackerror')->error('LoginController@registro (api) Excepción no controlada', [$e->getMessage()]);
            return response()->json(["errors" => 'Error interno del servidor'], 500);
        }

    }

}
