<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';

    protected $fillable = ['estudiante_id', 'responsable_id', 'titulo', 'descripcion', 'tipo', 'prioridad', 'estado', 'ubicacion', 'fecha_cierre'];

    protected function casts(): array
    {
        return ['fecha_cierre' => 'datetime'];
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialSolicitud::class)->orderBy('created_at');
    }
}
