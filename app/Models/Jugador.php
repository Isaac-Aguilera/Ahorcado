<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;
    
    protected $attributes = [
        'id' => 0,
        'nombre' => "",
        'superheroe' => "",
        "palabra" => "",
        'fallos' => [],
        'aciertos' => [],
        'vidas' => 6,
        'jugando' => true,
    ];

}
