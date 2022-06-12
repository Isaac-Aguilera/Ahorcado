<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;

class JugadoresController extends Controller
{
    // Funcion para crear la palabra a partir del nombre del superheroe.
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
     * Crea un jugador y le assigna los datos que han passado.
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
