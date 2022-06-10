<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\JugadoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function empezar(Request $request) {
        $jugadores = [];
        $opciones = explode(";", file_get_contents("superheroes.json"));
        array_pop($opciones);
        for ($i=0; $i < $request->numeroJugadores; $i++) { 
            $nombre = $request["jugador".strval($i)];
            $superheroe = $opciones[random_int(0,count($opciones)-1)];
            array_push($jugadores, JugadoresController::create($nombre,$superheroe,$i));
        }
        $request->session()->put('jugadores', $jugadores);
        return view('partida')->with(['jugador' => $jugadores[0]]);
    }

    public function reset(Request $request) {
        $jugadoresAntiguo = $request->session()->get('jugadores');
        $jugadoresNuevo = [];
        $opciones = explode(";", file_get_contents("superheroes.json"));
        array_pop($opciones);
        for ($i=0; $i < count($jugadoresAntiguo)-1; $i++) { 
            $nombre = $jugadoresAntiguo[$i]->nombre;
            $superheroe = $opciones[random_int(0,count($opciones)-1)];
            array_push($jugadoresNuevo , JugadoresController::create($nombre,$superheroe,$i));
        }
        $request->session()->put('jugadores', $jugadoresNuevo );
        $jugador = $jugadoresNuevo[0];
        return response()->json([
            'id' => $jugador->id,
            'nombre' => $jugador->nombre,
            'fallos' => $jugador->fallos,
            'vidas' => $jugador->vidas,
            'palabra' => $jugador->palabra,
        ]);;
    }

    public function jugar(Request $request) {
        $jugadores = $request->session()->get('jugadores');
        $id = $request['id'];
        $letra = strtoupper($request['letra']);
        $jugador = $jugadores[$id];
        $seguir = false;

        if(str_contains($jugador->superheroe, $letra) || str_contains($jugador->superheroe, strtolower($letra))) {
            if(in_array($letra, $jugador->aciertos)) {
                $jugador->vidas -= 1;
            } else {
                $seguir = true;
                $jugador->aciertos = array_merge($jugador->aciertos, array($letra));
                $caracteres = str_split($jugador->superheroe);
                $jugador->palabra = "";
                foreach($caracteres as $caracter){
                    if($caracter == " " || $caracter == "-") {
                        $jugador->palabra.=$caracter;
                    } else if(in_array(strtoupper($caracter),$jugador->aciertos)) {
                        $jugador->palabra.=$caracter;
                    } else {
                        $jugador->palabra.="_";
                    }
                }
            }

        } else {
            if(in_array($letra, $jugador->fallos)) {
                $jugador->vidas -= 1;
            } else {
                $jugador->vidas -= 1;
                $jugador->fallos = array_merge($jugador->fallos, array($letra));
            }
        }

        $jugadores[$jugador->id] = $jugador;
        $request->session()->put('jugadores', $jugadores);

        if($seguir) {
            if($jugador->superheroe == $jugador->palabra) {
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                    'ganador' => true,
                ]);
            } else {
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                ]);
            }

        } else {
            $perdedor = "";
            if($jugador->vidas == 0) {
                $perdedor = $jugador;
            }
            $fin = 0;
            $n = $jugador->id;
            $loop = true;
            while ($loop) {
                if (count($jugadores)-1 == $n) {
                    if ($jugadores[0]->vidas != 0) {
                        $loop = false;
                    }
                    $n = 0;
                } else {
                    if ($jugadores[$n+1]->vidas != 0) {
                        $loop = false;
                    }
                    $n += 1;
                }
                $fin += 1;
                if(count($jugadores) == $fin){
                    $loop = false; 
                }
            }

            if(count($jugadores) == $fin) {
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                    'fin' => true,
                ]);
            } else if($perdedor != "") {
                $jugador = $jugadores[$n];
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                    'perdedor' => $perdedor->nombre,
                ]);
            } else {
                $jugador = $jugadores[$n];
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                ]);
            }
            
        }

    }
    
}
