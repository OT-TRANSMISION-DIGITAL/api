<?php

namespace App\Models;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Sucursal;
use App\Models\User;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'motivo',
        'fechaHoraSolicitud',
        'fechaHoraLlegada',
        'fechaHoraSalida',
        'direccion',
        'estatus',
        'cliente_id',
        'tecnico_id',
        'sucursal_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($orden) {
            $existingOrder = self::where('tecnico_id', $orden->tecnico_id)
                ->where('fechaHoraSolicitud', $orden->fechaHoraSolicitud)
                ->first();

            if ($existingOrder) {
                throw new CustomException('El técnico ya tiene una orden asignada en la misma fecha y hora');
            }
        });
    }
}
