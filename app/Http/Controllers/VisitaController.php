<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Visita\VisitaRequest;
use Illuminate\Database\QueryException;

class VisitaController extends Controller
{

    public function index(Request $request)
    {
        $visitas = Visita::select('id', 'motivo', 'fechaHoraSolicitud', 'fechaHoraLlegada', 'fechaHoraSalida', 'direccion', 'estatus', 'cliente_id', 'tecnico_id', 'sucursal_id')
            ->with(['cliente', 'tecnico', 'sucursal']);

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        
        $estatus = $request->get('estatus', '');
        switch ($estatus) {
            case 'Sin Autorizar':
                $visitas->where('estatus', 'Sin Autorizar');
                break;
            case 'Autorizada':
                $visitas->where('estatus', 'Autorizada');
                break;
            case 'Finalizada':
                $visitas->where('estatus', 'Finalizada');
                break;
            case 'Cancelada':
                $visitas->where('estatus', 'Cancelada');
                break;
            default:
                break;
        }

        $offset = $page == 1 ? 0 : $perPage * ($page - 1);
        $total = $visitas->count();
        $visitas->limit($perPage)->offset($offset);

        $firstItem = ($page - 1) * $perPage + 1;
        $lastItem = min($page * $perPage, $total);

        try {
            $visitas = $visitas->get();
            return response()->json([
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'firstItem' => $firstItem,
                'lastItem' => $lastItem,
                'data' => $visitas
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener las visitas",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(VisitaRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $visita = Visita::create([
                'motivo' => $validatedData['motivo'],
                'fechaHoraSolicitud' => $validatedData['fechaHoraSolicitud'],
                'fechaHoraLlegada' => $validatedData['fechaHoraLlegada'],
                'fechaHoraSalida' => $validatedData['fechaHoraSalida'],
                'direccion' => $validatedData['direccion'],
                'cliente_id' => $validatedData['cliente_id'],
                'tecnico_id' => $validatedData['tecnico_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
                'estatus' => 'Sin Autorizar',

            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al crear la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita creada con éxito',
            'visita' => $visita->id
        ], 200);
    }

    public function show($id)
    {
        $visita = Visita::select('id', 'motivo', 'fechaHoraSolicitud', 'fechaHoraLlegada', 'fechaHoraSalida', 'direccion', 'estatus', 'cliente_id', 'tecnico_id', 'sucursal_id')
            ->with(['cliente', 'tecnico', 'sucursal'])->find($id);

        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        return response()->json($visita, 200);
    }

    public function update(VisitaRequest $request, $id)
    {
        $validatedData = $request->validated();

        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'motivo' => $validatedData['motivo'],
                'fechaHoraSolicitud' => $validatedData['fechaHoraSolicitud'],
                'fechaHoraLlegada' => $validatedData['fechaHoraLlegada'],
                'fechaHoraSalida' => $validatedData['fechaHoraSalida'],
                'direccion' => $validatedData['direccion'],
                'cliente_id' => $validatedData['cliente_id'],
                'tecnico_id' => $validatedData['tecnico_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al actualizar la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita actualizada con éxito',
            'visita' => $visita->id
        ], 200);
    }

    public function autorizar($id)
    {
        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'estatus' => 'Autorizada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al autorizar la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita autorizada con éxito'
        ], 200);
    }

    public function finalizar($id)
    {
        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'estatus' => 'Finalizada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al finalizar la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita finalizada con éxito'
        ], 200);
    }

    public function cancelar($id)
    {
        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'estatus' => 'Cancelada'
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al cancelar la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita cancelada con éxito'
        ], 200);
    }
}