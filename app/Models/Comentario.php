<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $fillable = ['solicitud_id', 'user_id', 'contenido'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
