<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <header>
        <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
            <div class="container-fluid">
              <h2 id="nombre" class="navbar-brand">
                Jugador: {{ $jugador->nombre }}
              </h2>
              <img id="headerImage" src="" style="height: 6vh;" class="img-fluid" alt="">
            </div>
          </nav>
    </header>
    <body class="antialiased">
        <h1 class="fs-1 text-center mt-5">Ahorcado de superheroes</h1>
        <div id="palabra" class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center w-75">
            <h2>{{ $jugador->palabra }}</h2>
        </div>
        <div id="fallos">
            
        </div>
        <div id="modal"></div>
    </body>
    <footer class="position-fixed bottom-0 start-50 translate-middle">
        <p class="">Website provided by Isaac Aguilera Cano || Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) </p>
    </footer>
</html>
<script>
    document.getElementById('headerImage').src = "img/"+superheroeAleatorio();
    var id = "{{ $jugador->id }}";

    function superheroeAleatorio() {
        var texto = "AngryHulk.png,Batman.png,capitanA.png,HappyHulk.png,IronMan.png,PencilThor.png,superhero1.png,superhero2.png,superhero3.png";
        opciones = texto.split(",");
        posicionAleatoria = Math.floor(Math.random() * opciones.length);
        return opciones[posicionAleatoria];
    }

    function reset() {
        $.ajax({
            url: '/reset',
            method: 'post',
            data: {
                '_token': "{{ csrf_token() }}",
            },
            error: function (response) {
                console.log(response);
            },
            success: function (response) {
                /*
                    Si la peticion ajax funciona correctamente se muestra el nuevo contenido.
                */
                console.log(response);
                id = response['id'];
                document.getElementById('nombre').innerHTML = 'Jugador:'+response['nombre'];
                document.getElementById('fallos').innerHTML = '<h3>Letras falladas</h3>';
                response['fallos'].forEach(letra => {
                    document.getElementById('fallos').innerHTML += "<p>"+letra+"</p>"
                });
                document.getElementById('palabra').innerHTML = '<h2>'+response['palabra']+"</h2>";
            }
        });
    }

    
    document.addEventListener('keyup', (event) => {
        if(event.keyCode >= 65 && event.keyCode <= 90){
            $.ajax({
                url: '/jugar',
                method: 'post',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'letra': event.key,
                    'id': id,
                },
                error: function (response) {
                    console.log(response);
                },
                success: function (response) {
                    /*
                        Si la peticion ajax funciona correctamente se muestra el nuevo contenido.
                    */
                    id = response['id'];
                    document.getElementById('nombre').innerHTML = 'Jugador:'+response['nombre'];
                    document.getElementById('fallos').innerHTML = '<h3>Letras falladas</h3>';
                    response['fallos'].forEach(letra => {
                        document.getElementById('fallos').innerHTML += "<p>"+letra+"</p>"
                    });
                    document.getElementById('palabra').innerHTML = '<h2>'+response['palabra']+"</h2>";
                    if (response['ganador']) {
                        document.getElementById('modal').innerHTML = `
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ganador!!!</h5>
                                </div>
                                <div class="modal-body">
                                    <h3>El jugador `+response['nombre']+` ha ganado!</h3>
                                    <p>La palabra era: `+response['palabra']+`</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="window.location='{{ url("home") }}'" class="btn btn-secondary">Nuevos jugadores</button>
                                    <button type="button" onclick="reset();" data-bs-dismiss="modal" class="btn btn-primary">Seguir los mismos jugadores</button>
                                </div>
                                </div>
                            </div>
                        </div>`;
                        new bootstrap.Modal($('#staticBackdrop')).show();
                    } else if (response['perdedor']) {
                        document.getElementById('modal').innerHTML = `
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Perdedor</h5>
                                </div>
                                <div class="modal-body">
                                    <h3>El jugador `+response['perdedor']+` ha perdido!</h3>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="window.location='{{ url("home") }}'" class="btn btn-secondary">Nuevos jugadores</button>
                                    <button type="button" data-bs-dismiss="modal" class="btn btn-primary">Seguir jugando</button>
                                </div>
                                </div>
                            </div>
                        </div>`;
                        new bootstrap.Modal($('#staticBackdrop')).show();
                    } else if (response['fin']) {
                        document.getElementById('modal').innerHTML = `
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ha terminado!</h5>
                                </div>
                                <div class="modal-body">
                                    <h3>Todos los jugadores han perdido!</h3>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="window.location='{{ url("home") }}'" class="btn btn-secondary">Nuevos jugadores</button>
                                    <button type="button" onclick="reset()" data-bs-dismiss="modal" class="btn btn-primary">Seguir los mismos jugadores</button>
                                </div>
                                </div>
                            </div>
                        </div>`;
                        new bootstrap.Modal($('#staticBackdrop')).show();
                    }
                }
            });
        }
    });
     

</script>