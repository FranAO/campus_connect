<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $fillable = ['solicitud_id', 'nombre_original', 'ruta', 'tipo_archivo'];

    protected $appends = ['url'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->ruta);
    }
}
