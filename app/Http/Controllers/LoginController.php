<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Mail\CodigoAuthCorreo;
use Illuminate\Support\Facades\Mail;



class LoginController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'correo' => 'required|email',
                'password' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    "msg"=>"No se cumplieron las validaciones",
                    "errors" => $validator->errors()
                ], 400);
            }

            //Verificar si el usuario existe    
            $user = User::where("correo", "=", $request->correo)->first();

            //Si el usuario existe
            if ($user !== null) {
                // Verificar la contraseña
                if (Hash::check($request->password, $user->password)) {

                    // Si el usuario es un administrador, generar la ruta firmada sin intentar autenticarlo
                    //Y mandara el codigo al correo
                    if ($user->rol_id == 1) {

                    $url = URL::temporarySignedRoute('validarCodigo', now()->addMinutes(10), [
                        'id' => $user->id
                    ]);                        
                    // Generar numero aleatorio, convertirlo a string y hashear
                    $random = sprintf("%04d", rand(0, 9999));
                    $codigo = strval($random); //convertir a string
                    $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT);
                    //Guardarlo en BD 
                    $user->codigo = $codigo_hash;
                    $user->save();
                    //mandar mail con el codigo
                    $emailAdmin = new CodigoAuthCorreo($codigo);
                    Mail::to($user->correo)->send($emailAdmin);

                    return response()->json([
                        'rutaFirmada' => $url,
                        'msj' => 'Se ha enviado un correo con el código de autenticación.',
                    ], 200);

                    }
                    else{

                    // Si el usuario no es un administrador, autenticarlo
                    return response()->json([
                        'token' => $user->createToken("auth_token")->plainTextToken,
                        'usuario' => $user
                    ], 200);   

                    }
                    

                } else {
                    //Si se equivoco en la contraseña
                    return response()->json([
                        "msg"=>"credenciales incorrectas",
                        ],400);
                }
            }
            //Si el usuario no existe 
            else {
                return response()->json([
                    "msg"=>"credenciales incorrectas",
                    ],400);
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

    public function logout($id)
    {
        try {
            //Buscar Usuario
            $user = User::find($id);

            if ($user === null) {
                return response()->json([
                    'msg' => 'Acceso no autorizado',
                ], 403);
            }

            $user->tokens()->delete();

            return response()->json([
                'msg' => 'Seccion cerrada',
            ],200);

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

    public function validarCodigo($id, Request $request)
    {
        try {

            //Si la ruta firmada no es valida
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'msg' => 'Acceso no autorizado',
                ], 403);
            }

            //validar que viene el codigo
            $validator = Validator::make(
                $request->all(),
                [
                    'codigo' => 'required',
                ]
            );
    
            if ($validator->fails()) {
                return response()->json([
                    "msg"=>"No se cumplieron las validaciones",
                    "errors" => $validator->errors()
                ], 400);
            }

            //Buscar Usuario
            $user = User::find($id);

            if ($user === null) {
                return response()->json([
                    'msg' => 'Acceso no autorizado',
                ], 403);
            }

            //Si es admin va a validar que el codigo sea correcto, si es asi logear a admin
            if (password_verify($request->codigo, $user->codigo)) {

                    return response()->json([
                        'token' => $user->createToken("auth_token")->plainTextToken,
                        'usuario' => $user
                    ], 200); 

            }
            else{
                return response()->json([
                    'msg' => 'Codigo incorrecto',
                ], 403);
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

    public function prueba()
    {
        $codigo= '123456';
        $email = new CodigoAuthCorreo($codigo);
        Mail::to('miguelflow668@gmail.com')->send($email);
        
        return response()->json([
            'msg' => 'Ok'
        ], 200);
    }


}
