<?php

namespace App\Http\Controllers;

// Use Controllers
use App\Http\Controllers\Controller;
// Use Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ValidateCodeRequest;
// Use Models
use App\Models\User;
// Use Mails
use App\Mail\CodigoAuthCorreo;
// Use Facades
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
// Use Exceptions
use Illuminate\Database\QueryException;
use Exception;



class LoginController extends Controller
{

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $validateData = $request->validated();
        try {
            //Verificar si el usuario existe    
            $user = User::where("correo", "=", $validateData['correo'])->first();
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "Usuario no encontrado."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        //Si el usuario no existe
        if ($user == null) return response()->json([
            "msg"=>"credenciales incorrectas",
            ],400);

        //Si el usuario se equivoco en la contraseña
        if(!Hash::check($request->password, $user->password)) return response()->json([
            "msg"=>"credenciales incorrectas",
            ],400);
        // Si el usuario no es un administrador, autenticarlo
        if (!($user->rol_id == 1)) return response()->json([
            'token' => $user->createToken("auth_token")->plainTextToken,
            'usuario' => $user
        ], 200); 

        // Si el usuario es un administrador, generar la ruta firmada sin intentar autenticarlo
        //Y mandara el codigo al correo
        $url = URL::temporarySignedRoute('validarCodigo', now()->addMinutes(10), [
            'id' => $user->id
        ]);                        
        // Generar numero aleatorio, convertirlo a string y hashear
        $random = sprintf("%04d", rand(0, 9999));
        $codigo = strval($random); //convertir a string
        $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT);
        
        try {
            //Guardarlo en BD 
            $user->codigo = $codigo_hash;
            $user->save();

            //mandar mail con el codigo
            $emailAdmin = new CodigoAuthCorreo($codigo);
            Mail::to($user->correo)->send($emailAdmin);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "Error al generar el codigo."
            ], 500);
        } catch (Exception $e) {
            // Manejo de cualquier otra excepción no prevista
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'rutaFirmada' => $url,
            'msj' => 'Se ha enviado un correo con el código de autenticación.',
        ], 200);
    }

    public function logout(Request $request)
    {
        // Recuperar el usuario autenticado
        $user = $request->user();
        // Revocar todos los tokens del usuario
        $user->tokens()->delete();
        // Retornar respuesta
        return response()->json([
            'msg' => 'Seccion cerrada',
        ],200);
    }

    public function validarCodigo($id, ValidateCodeRequest $request)
    {
        //Si la ruta firmada no es valida
        if (!$request->hasValidSignature()) {
            return response()->json([
                'msg' => 'Acceso no autorizado',
            ], 403);
        }
        $validateData = $request->validated();
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
        if (!password_verify($validateData["codigo"], $user->codigo)) return response()->json([
            'msg' => 'Codigo incorrecto',
        ], 403);
    
        //Si es admin va a validar que el codigo sea correcto, si es asi logear a admin
        return response()->json([
            'token' => $user->createToken("auth_token")->plainTextToken,
            'usuario' => $user
        ], 200); 

    }


}
