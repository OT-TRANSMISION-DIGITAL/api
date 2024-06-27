<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sucursal;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $fillable = [
        'nombre',
        'direccion',
        'correo',
        'telefono',
        'estatus'
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
}
