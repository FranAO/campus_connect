<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialSolicitud extends Model
{
    public $timestamps = false;

    protected $table = 'historial_solicitudes';

    protected $fillable = ['solicitud_id', 'user_id', 'accion', 'estado_anterior', 'estado_nuevo', 'descripcion'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
