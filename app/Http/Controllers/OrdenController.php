<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Orden\OrdenRequest;
use Illuminate\Database\QueryException;
use App\Models\OrdenDetalle;

class OrdenController extends Controller
{

    public function index(Request $request)
    {
        $ordenes = Orden::with(['cliente', 'tecnico', 'sucursal', 'detalles.producto']);

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $estatus = $request->get('estatus', '');
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

        return response()->json([
            'msg' => 'Orden Autorizada con éxito'
        ], 200);
    }

    public function finalizar($id){
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
}

