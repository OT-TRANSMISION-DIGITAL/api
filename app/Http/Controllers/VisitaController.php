<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Visita\VisitaRequest;
use Illuminate\Database\QueryException;
use App\Models\Orden;
use App\Models\User;
use App\Exceptions\CustomException;

//Eventos
use App\Events\Notificaciones;
use App\Events\NotificacionesAdmin;


class VisitaController extends Controller
{

    public function index(Request $request)
    {
        $visitas = Visita::select('id', 'motivo', 'fechaHoraSolicitud', 'fechaHoraLlegada', 'fechaHoraSalida',  'coorLlegada', 'coorSalida','direccion', 'estatus', 'cliente_id', 'tecnico_id', 'sucursal_id')
            ->with(['cliente', 'tecnico', 'sucursal'])
            ->orderBy('fechaHoraSolicitud', 'desc');

        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);

        $estatus = $request->get('estatus', '');
        $tecnico = $request->get('tecnico', null);
        $fecha = $request->get('fecha', null);

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

        if ($tecnico != null) {
            if (User::find($tecnico) == null) {
                return response()->json([
                    'msg' => 'El tecnico no existe',
                ], 404);
            }
            $visitas->where('tecnico_id', '=', $tecnico);
        } else {
            $visitas->with('tecnico');
        }

        if ($fecha != null) {
            $visitas->whereDate('fechaHoraSolicitud', '=', $fecha);
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
        } catch (CustomException $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => $e->getMessage(),
            ], 500);
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
        $visita = Visita::select('id', 'motivo', 'fechaHoraSolicitud', 'fechaHoraLlegada', 'fechaHoraSalida', 'coorLlegada', 'coorSalida', 'direccion', 'estatus', 'cliente_id', 'tecnico_id', 'sucursal_id')
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
        //NOTIFICACION PARA EL TECNICO CUANDO SE AUTORIZA UNA VISITA
        $message = 'Tienes una nueva visita asignada #' . $visita->id;

        event(new Notificaciones($message, $visita->tecnico_id));

        return response()->json([
            'msg' => 'Visita autorizada con éxito'
        ], 200);
    }

    public function finalizar(Request $request, $id)
    {
        $fechaHoraSalida = $request->get('fechaHoraSalida', null);
        $coorSalida = $request->get('coorSalida', null);

        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'estatus' => 'Finalizada',
                'fechaHoraSalida' => $fechaHoraSalida,
                'coorSalida' => $coorSalida
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

        $tecnico = User::find($visita->tecnico_id);

        //NOTIFICACION PARA EL ADMIN CUANDO UNA VISITA SE FINALIZO
        $message = 'El tecnico ' . $tecnico->nombre . ' ha finalizado la visita #' . $visita->id;

        event(new NotificacionesAdmin($message));

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

    public function agenda(Request $request)
    {
        $fechaHoy = date('Y-m-d');
        //Si no hay fecha te trae todas
        $fecha = $request->get('fecha', null);
        //Si no hay tipo se toma el de ordenes
        $tipo = $request->get('tipo', 'ordenes');
        //id del tecnico
        $tecnico = $request->get('tecnico', null);
        //estatus
        $estatus = $request->get('estatus', null);



        $ordenes = Orden::select('id', 'fechaHoraSolicitud', 'estatus', 'tecnico_id')
            ->where('estatus', '!=', 'Sin Autorizar');
        $visitas = Visita::select('id', 'fechaHoraSolicitud', 'estatus', 'tecnico_id')
            ->where('estatus', '!=', 'Sin Autorizar');

        if ($fecha != null) {
            $ordenes->whereDate('fechaHoraSolicitud', '=', $fecha);
            $visitas->whereDate('fechaHoraSolicitud', '=', $fecha);
        }

        switch ($estatus) {
            case 'Sin Autorizar':
                $visitas->where('estatus', 'Sin Autorizar');
                $ordenes->where('estatus', 'Sin Autorizar');
                break;
            case 'Autorizada':
                $visitas->where('estatus', 'Autorizada');
                $ordenes->where('estatus', 'Autorizada');
                break;
            case 'Finalizada':
                $visitas->where('estatus', 'Finalizada');
                $ordenes->where('estatus', 'Finalizada');
                break;
            case 'Cancelada':
                $visitas->where('estatus', 'Cancelada');
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
            $visitas->where('tecnico_id', '=', $tecnico);
        } else {
            $ordenes->with('tecnico');
            $visitas->with('tecnico');
        }

        try {
            if ($tipo == 'ordenes') {
                $ordenes = $ordenes->get();
                return response()->json($ordenes, 200);
            } else {
                $visitas = $visitas->get();
                return response()->json($visitas, 200);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                "msg" => "Error al obtener la agenda",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function notificacion()
    {
        $message = 'prueba notificaciones';

        event(new Notificaciones($message, 1));
        event(new NotificacionesAdmin($message));
    }

    public function horariosTecnico(Request $request)
    {

        try {

            $fecha = $request->get('fecha', null);
            $tecnico = $request->get('tecnico', null);

            if (($fecha == null) || ($tecnico == null)) {
                return response()->json([
                    'msg' => 'fecha y tecnico son requeridos',
                ], 422);
            }

            $horario = ["08:00:00", "11:00:00", "14:00:00", "17:30:00", "18:00:00", "19:00:00", "20:00:00"];

            $visitas = Visita::select('fechaHoraSolicitud')
                ->where('tecnico_id', '=', $tecnico)
                ->where('estatus', '!=', 'Cancelada')
                ->where('estatus', '!=', 'Finalizada')
                ->whereDate('fechaHoraSolicitud', '=', $fecha)
                ->get()
                ->pluck('fechaHoraSolicitud')
                ->toArray();

            // Obtener las fechas y horas ocupadas en la tabla Ordenes
            $ordenes = Orden::select('fechaHoraSolicitud')
                ->where('tecnico_id', '=', $tecnico)
                ->where('estatus', '!=', 'Cancelada')
                ->where('estatus', '!=', 'Finalizada')
                ->whereDate('fechaHoraSolicitud', '=', $fecha)
                ->get()
                ->pluck('fechaHoraSolicitud')
                ->toArray();

            // Unir las fechas ocupadas de ambas tablas
            $ocupados = array_merge($visitas, $ordenes);


            // Convertir las fechas ocupadas a solo horas en formato H:i:s usando funciones nativas de PHP
            $ocupados = array_map(function ($item) {
                return date('H:i:s', strtotime($item));
            }, $ocupados);

            // Filtrar los horarios disponibles
            $disponibles = array_diff($horario, $ocupados);

            // Convertir los horarios disponibles en un array sin índices asociados
            $disponibles = array_values($disponibles);

            // Obtener la hora actual solo si la fecha es hoy
            if ($fecha == date('Y-m-d')) {
                $horaActual = date('H:i:s');

                // Filtrar horarios disponibles que son mayores a la hora actual
                $disponibles = array_filter($disponibles, function ($hora) use ($horaActual) {
                    return $hora > $horaActual;
                });

                // Reindexar el array
                $disponibles = array_values($disponibles);
            }
        } catch (Exception $e) {
            return response()->json([
                'msg' => 'Error al obtener los horarios del tecnico',
                'error' => $e->getMessage()
            ], 500);
        }


        return response()->json([
            'msg' => 'Horarios obtenidos con exito',
            'horarios' => $disponibles,
            'ocupados' => $ocupados
        ], 200);
    }

    public function horaLlegada(Request $request, $id)
    {
        //Si no hay fecha te trae todas
        $fechaHoraLlegada = $request->get('fechaHoraLlegada', null);
        //Si no hay tipo se toma el de ordenes
        $coorLlegada = $request->get('coorLlegada', null);

        if (($fechaHoraLlegada == null) || ($coorLlegada == null)) {
            return response()->json([
                'msg' => 'fechaHoraLlegada y coorLlegada son requeridos',
            ], 422);
        }

        $visita = Visita::find($id);
        if (!$visita) {
            return response()->json([
                'msg' => 'Visita no encontrada',
            ], 404);
        }

        try {
            $visita->update([
                'fechaHoraLlegada' => $fechaHoraLlegada,
                'coorLlegada' => $coorLlegada,
            ]);
        } catch (QueryException $e) {
            Log::error('Error de consulta SQL: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "Error al registrar la hora de llegada."
            ], 500);
        } catch (Exception $e) {
            Log::error('Excepción no controlada: ' . $e->getMessage());
            return response()->json([
                "error" => 'Error interno del servidor.',
                "message" => "No se pudo resolver la petición."
            ], 500);
        }

        return response()->json([
            'msg' => 'Hora de llegada registrada con éxito en visita #' . $visita->id
        ], 200);
    }
}
