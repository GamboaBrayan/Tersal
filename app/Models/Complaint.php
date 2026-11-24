<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'generated_id',
        'es_menor',
        'nombre_apoderado',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'direccion',
        'distrito',
        'tipo_bien',
        'monto_reclamado',
        'descripcion_bien',
        'tipo_reclamo',
        'detalle',
        'pedido',
    ];

    protected $casts = [
        'es_menor' => 'boolean',
        'monto_reclamado' => 'decimal:2',
    ];
}

