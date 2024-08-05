<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $table = 'ordenes';

    protected $fillable = [
        'persona_solicitante',
        'puesto',
        'firma',
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

    public function detalles()
    {
        return $this->hasMany(OrdenDetalle::class, 'orden_id');
    }
}
