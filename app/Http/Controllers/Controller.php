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

    // Funcion para empezar la partida.
    public function empezar(Request $request) {
        $jugadores = [];
        // Recoje los superheroes del archivo.
        $opciones = explode(";", file_get_contents("superheroes.json"));
        array_pop($opciones);

        // Crea la cantidad de jugadores que se haya indicado con los nombres recogidos, se le asigna un superheroe y los mete en un array.
        for ($i=0; $i < $request->numeroJugadores; $i++) { 
            $nombre = $request["jugador".strval($i)];
            $superheroe = $opciones[random_int(0,count($opciones)-1)];
            array_push($jugadores, JugadoresController::create($nombre,$superheroe,$i));
        }

        // Guarda los jugadores.
        $request->session()->put('jugadores', $jugadores);

        // Abre la View y le manda el primer jugador.
        return view('partida')->with(['jugador' => $jugadores[0]]);
    }

    // Funcion para resetear la partida con los mismos nombres.
    public function reset(Request $request) {
        // Recoje los jugadores antiguos.
        $jugadoresAntiguo = $request->session()->get('jugadores');

        $jugadoresNuevo = [];

        // Recoje los superheroes del archivo.
        $opciones = explode(";", file_get_contents("superheroes.json"));
        array_pop($opciones);

        // Crea la cantidad de jugadores que habia en la anterior partida con los mismos nombres, se le asigna un nuevo superheroe y los mete en un array.
        for ($i=0; $i < count($jugadoresAntiguo); $i++) { 
            $nombre = $jugadoresAntiguo[$i]->nombre;
            $superheroe = $opciones[random_int(0,count($opciones)-1)];
            array_push($jugadoresNuevo , JugadoresController::create($nombre,$superheroe,$i));
        }

        // Guarda los nuevos jugadores
        $request->session()->put('jugadores', $jugadoresNuevo );

        // Retorna el primer jugador.
        $jugador = $jugadoresNuevo[0];
        return response()->json([
            'id' => $jugador->id,
            'nombre' => $jugador->nombre,
            'fallos' => $jugador->fallos,
            'vidas' => $jugador->vidas,
            'palabra' => $jugador->palabra,
        ]);;
    }


    // Funcion para jugar un turno.
    public function jugar(Request $request) {
        // Recoge los jugadores guardados.
        $jugadores = $request->session()->get('jugadores');

        $id = $request['id'];
        $letra = strtoupper($request['letra']);
        $jugador = $jugadores[$id];
        $seguir = false;

        // Comprueba que la letra que ha elegido tanto en minuscular como en mayusculas esta en la palabra que tiene que adivinar.
        if(str_contains($jugador->superheroe, $letra) || str_contains($jugador->superheroe, strtolower($letra))) {

            // Compruba si ya habia acertado la letra elegida, si es asi le resta una vida.
            if(in_array($letra, $jugador->aciertos)) {
                $jugador->vidas -= 1;
            } else {
                // En este caso añade a la array aciertos la letra y actualiza la palabra.

                // Para que siga jugando el mismo jugador.
                $seguir = true;

                // Añade a la array aciertos la letra.
                $jugador->aciertos = array_merge($jugador->aciertos, array($letra));

                // Actualiza la palabra.
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
            // En este caso se le resta una vida al jugador y si la letra no esta dentro de fallos la añade.

            if(in_array($letra, $jugador->fallos)) {
                $jugador->vidas -= 1;
            } else {
                $jugador->vidas -= 1;
                $jugador->fallos = array_merge($jugador->fallos, array($letra));
            }
        }
        
        // Guarda los cambios.
        $jugadores[$jugador->id] = $jugador;
        $request->session()->put('jugadores', $jugadores);
        
        // Compruba si sigue el mismo jugador.
        if($seguir) {
            // Comprueba si el jugador ha ganado y devuelve los datos correspondientes.
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
                // En este caso devuelve los datos correspondientes.

                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                ]);
            }

        } else {
            // En este caso se comprueba si el jugador o jugadores han perdido y se procede correspondientemente.

            $perdedor = "";
            
            // Se compruba si el jugador ha perdido.
            if($jugador->vidas == 0 && $jugador->jugando) {
                $jugador->jugando = false;
                $perdedor = $jugador;
            }

            // comprueba si todos los jugadores han perdido y pasa al siguiente jugador.
            $fin = 0;
            $n = $jugador->id;
            $loop = true;
            while ($loop) {
                // Comprueba si el jugador es el ultimo de la array y si el siguiente jugador sigue jugando.
                if (count($jugadores)-1 == $n) {
                    $n = 0;
                    if ($jugadores[$n]->jugando) {
                        $loop = false;
                    }
                    
                } else {
                    // En este caso se compruba si el siguiente jugador sigue jugando.

                    $n += 1;
                    if ($jugadores[$n]->jugando) {
                        $loop = false;
                    }
                    
                }

                // Añade 1 a la cuenta para saber si todos los jugadores han perdido
                $fin += 1;

                // Compruba que todos los jugadores han perdido.
                if(count($jugadores) == $fin){
                    $loop = false; 
                }
            }

            // Compruba que todos los jugadores han perdido y devuelve los datos correspondientes.
            if(count($jugadores) == $fin) {
                $jugador = $jugadores[$n];
                return response()->json([
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'fallos' => $jugador->fallos,
                    'vidas' => $jugador->vidas,
                    'palabra' => $jugador->palabra,
                    'fin' => true,
                ]);
            } else if($perdedor != "") {
                // En este caso compruba si el jugador ha perdido y devuelve los datos correspondientes.

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
                // En este caso passa al siguiente jugador y devuelve los datos correspondientes.

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
