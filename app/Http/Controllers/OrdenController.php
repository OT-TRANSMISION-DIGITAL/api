<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Orden\OrdenRequest;
use Illuminate\Database\QueryException;
use App\Models\OrdenDetalle;
use App\Http\Controllers\PdfController;
use App\Http\Requests\Imagen\FirmaRequest;

//Eventos
use App\Events\Notificaciones;
use App\Events\NotificacionesAdmin;

class OrdenController extends Controller
{

    public function index(Request $request)
    {
        $ordenes = Orden::with(['cliente', 'tecnico', 'sucursal', 'detalles.producto']);

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $estatus = $request->get('estatus', '');
        $tecnico = $request->get('tecnico', null);
        switch ($estatus) {
            case 'Sin Autorizar':
                $ordenes->where('estatus', 'Sin Autorizar');
                break;
            case 'Autorizada':
                $ordenes->where('estatus', 'Autorizada');
                break;
            case 'Finalizada':
                $ordenes->where('estatus', 'Finalizada');
                break;
            case 'Cancelada':
                $ordenes->where('estatus', 'Cancelada');
                break;
            default:
                break;
        }

        if ($tecnico != null) {
            if (User::find($tecnico) == null) {
                return response()->json([
                    'msg' => 'El tecnico no existe',
                ], 404);
            }
            $ordenes->where('tecnico_id', '=', $tecnico);
        } else {
            $ordenes->with('tecnico');
        }


        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $ordenes->count();
        $ordenes->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
            $ordenes = $ordenes->get();
            return response()->json([
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'firstItem' => $firstItem,
                'lastItem' => $lastItem,
                'data' => $ordenes
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener las órdenes",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function create(OrdenRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $orden = Orden::create([
                'fechaHoraSolicitud' => $validatedData['fechaHoraSolicitud'],
                'fechaHoraLlegada' => $validatedData['fechaHoraLlegada'],
                'fechaHoraSalida' => $validatedData['fechaHoraSalida'],
                'persona_solicitante' => $validatedData['persona_solicitante'],
                'puesto' => $validatedData['puesto'],
                'direccion' => $validatedData['direccion'],
                'cliente_id' => $validatedData['cliente_id'],
                'tecnico_id' => $validatedData['tecnico_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
                'estatus' => 'Sin Autorizar',
            ]);

            foreach ($validatedData['detalles'] as $detalle) {
                OrdenDetalle::create([
                    'cantidad' => $detalle['cantidad'],
                    'descripcion' => $detalle['descripcion'],
                    'estatus' => true,
                    'producto_id' => $detalle['producto_id'],
                    'orden_id' => $orden->id,
                ]);
            }
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear la orden."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Orden creada con éxito',
            'orden' => $orden->id
        ], 200);
    }

    public function show($id)
    {
        $orden = Orden::with(['cliente', 'tecnico', 'sucursal', 'detalles.producto'])->find($id);

        if (!$orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        return response()->json($orden, 200);
    }

    public function update(OrdenRequest $request, $id)
    {
        $validatedData = $request->validated();

        $orden = Orden::find($id);
        if (!$orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        try {
            $orden->update([
                'fechaHoraSolicitud' => $validatedData['fechaHoraSolicitud'],
                'fechaHoraLlegada' => $validatedData['fechaHoraLlegada'],
                'fechaHoraSalida' => $validatedData['fechaHoraSalida'],
                'persona_solicitante' => $validatedData['persona_solicitante'],
                'puesto' => $validatedData['puesto'],
                'direccion' => $validatedData['direccion'],
                'cliente_id' => $validatedData['cliente_id'],
                'tecnico_id' => $validatedData['tecnico_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
            ]);

            $orden->detalles()->delete();

            foreach ($validatedData['detalles'] as $detalle) {
                OrdenDetalle::create([
                    'cantidad' => $detalle['cantidad'],
                    'descripcion' => $detalle['descripcion'],
                    'estatus' => true,
                    'producto_id' => $detalle['producto_id'],
                    'orden_id' => $orden->id,
                ]);
            }
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al actualizar la orden."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Orden actualizada con éxito',
            'orden' => $orden->id
        ], 200);
    }

    public function autorizar($id)
    {
        $orden = Orden::find($id);
        if (!$orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        try {
            $orden->update([
                'estatus' => 'Autorizada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al Autorizar la orden."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        //NOTIFICACION PARA EL TECNICO CUANDO SE AUTORIZA UNA ORDEN
        $message = 'Tienes una nueva orden asignada #' . $orden->id;

        event(new Notificaciones($message, $orden->tecnico_id));

        return response()->json([
            'msg' => 'Orden Autorizada con éxito'
        ], 200);
    }

    public function finalizar($id)
    {
        $orden = Orden::find($id);
        if (!$orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        try {
            $orden->update([
                'estatus' => 'Finalizada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al finalizar la orden."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        $tecnico = User::find($orden->tecnico_id);

        //NOTIFICACION PARA EL ADMIN CUANDO UNA VISITA SE FINALIZO
        $message = 'El tecnico ' . $tecnico->nombre . ' ha finalizado la orden #' . $orden->id;

        event(new NotificacionesAdmin($message));

        return response()->json([
            'msg' => 'Orden Finalizada con éxito'
        ], 200);
    }

    public function cancelar($id)
    {
        $orden = Orden::find($id);
        if (!$orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        try {
            $orden->update([
                'estatus' => 'Cancelada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al cancelar la orden."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Orden Cancelada con éxito'
        ], 200);
    }

    public function generatePdf(Request $request)
    {
        $view = 'PDFS.ordenesServicio';
        $PdfController = new PdfController();

        // Obtener los datos de la solicitud
        $dataArray = $request->input(); // Obtener todo el contenido del request

        // Validar que los datos sean un array y contengan la clave 'data'
        if (!is_array($dataArray) || empty($dataArray)) {
            return response()->json(['error' => 'Formato de datos inválido.'], 400);
        }

        // Extraer el contenido de 'data' del primer objeto del array
        $data = [];
        if (isset($dataArray[0]['data'])) {
            $data = $dataArray[0]['data'];
        } else {
            return response()->json(['error' => 'Datos faltantes en la solicitud.'], 400);
        }

        // Parámetros - nombre del archivo, vista a la que va a hacer referencia, datos que va a mostrar en el PDF
        return $PdfController->generatePdf('ordenes', $view, $data);
    }

    //Este metodo es para insertar y cambiar la imagen, si tiene una imagen antes, la borra 
    public function guardarFirma(FirmaRequest $request, $id)
    {
        $validatedData = $request->validated();

        $Orden = Orden::find($id);

        if (!$Orden) {
            return response()->json([
                'msg' => 'Orden no encontrada',
            ], 404);
        }

        $image = $validatedData['firma'];
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $originalName = str_replace(' ', '_', $originalName);
        $timestamp = date('YmdHis');
        $extension = $image->getClientOriginalExtension();
        $name = $originalName . $timestamp . '.' . $extension;
        $destinationPath = public_path('/imagenes/firmas');
        if ($image->move($destinationPath, $name)) {
            $validatedData['firma'] = env('URL_IMAGENES') . 'firmas/' . $name;

            // Eliminar la imagen anterior si existe
            if ($Orden->firma) {
                $oldImagePath = public_path('/imagenes/firmas/' . basename($Orden->firma));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            return response()->json([
                'msg' => 'Error al subir la firma',
            ], 500);
        }

        try {
            $Orden->update([
                'firma' => $validatedData['firma']
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al guardar la firma."
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
            'msg' => 'Firma guardada con exito',
            'Orden' => $Orden->id
        ], 200);
    }

    public function eliminarFirma($id)
    {
        $Orden = Orden::find($id);

        if (!$Orden) {
            return response()->json([
                'msg' => 'Orden no encontrado',
            ], 404);
        }


        if ($Orden->firma != null) {
            $oldImagePath = public_path('/imagenes/firmas/' . basename($Orden->firma));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        try {
            $Orden->update([
                'firma' => null
            ]);
        } catch (QueryException $e) {
            // Manejo de la excepción de consulta SQL
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar la firma."
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
            'msg' => 'Firma eliminada con exito',
            'Orden' => $Orden->id
        ], 200);
    }
}