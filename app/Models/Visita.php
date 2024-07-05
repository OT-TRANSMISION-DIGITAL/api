<?php

namespace App\Models;

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
}
