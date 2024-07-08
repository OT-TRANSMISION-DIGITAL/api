<?php

namespace App\Http\Controllers\watchApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\Auth\ValidateCodeRequest;
// Use Exceptions
use Illuminate\Database\QueryException;
use Exception;

class LoginController extends Controller
{

    public function generateCode($id){
        try{
            $user = User::find($id);
            // Generar numero aleatorio, convertirlo a string y hashear
            $random = sprintf("%04d", rand(0, 9999));
            $code = strval($random); //convertir a string
            $code_hash = password_hash($code, PASSWORD_DEFAULT);
            $user->watch_codigo = $code_hash;
            $user->save();

            return response()->json([
                "code" => $code,
                "message" => "successfully"
            ], 200);

        }
        catch(QueryException $e){
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "Usuario no encontrado."
            ], 500);
        }
    }


    public function validateCode(ValidateCodeRequest $request){
        $validateData = $request->validated();
        $users = User::all();
        $id = 0;

        foreach($users as $user){
            if(password_verify($validateData["codigo"], $user->watch_codigo)){
                $id = $user->id;
            }
        }

        try {
            //Buscar Usuario
            $user = User::find($id);

            // Verificar si el usuario existe
            if ($user === null) return response()->json([
                    'msg' => 'Acceso no autorizado.',
                ], 403);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "Acceso no autorizado."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        // Verificar que el código sea correcto
        if (!password_verify($validateData["codigo"], $user->watch_codigo)) return response()->json([
            'msg' => 'Codigo incorrecto',
        ], 403);

       //si el codigo es correcto iniciar sesion en el reloj
        return response()->json([
            'token' => $user->createToken("auth_token")->plainTextToken,
            'usuario' => $user
        ], 200);
    }
}
