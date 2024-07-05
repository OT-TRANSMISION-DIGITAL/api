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
            ->with(['cliente', 'tecnico', 'sucursal'])->where('estatus', true);

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
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
                'estatus' => true,

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
                'estatus' => true,

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

    public function delete($id)
    {
        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'estatus' => false
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al eliminar la visita."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Visita eliminada con éxito'
        ], 200);
    }
}
