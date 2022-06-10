<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;

class JugadoresController extends Controller
{
    public static function inicializarPalabra($superheroe) {

        $palabra = "";
        $caracteres = str_split($superheroe);
        foreach($caracteres as $caracter){
            if($caracter == " " || $caracter == "-") {
                $palabra.=$caracter;
            } else {
                $palabra.="_";
            }
        }

        return $palabra;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create($nombre, $superheroe, $id) {
        $jugador = new Jugador();
        $jugador->id = $id;
        $jugador->nombre = $nombre;
        $jugador->superheroe = $superheroe;
        $jugador->palabra = JugadoresController::inicializarPalabra($superheroe);
        return $jugador;
    }

    

}
