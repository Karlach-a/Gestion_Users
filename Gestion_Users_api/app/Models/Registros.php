<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'fecha_hora',
        'accion',
        
    ];
}
