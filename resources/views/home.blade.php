<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/home.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <header>
        <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
            <div class="container-fluid">
              <a class="navbar-brand border border-secondary p-2 rounded-pill" href="#">
                Ahorcado
              </a>
              <img id="headerImage" src="" style="height: 6vh;" class="img-fluid" alt="">
            </div>
          </nav>
    </header>
    <body class="antialiased">
        <h1 class="fs-1 text-center mt-2 mt-md-5">Ahorcado de superheroes</h1>
        <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center w-75">
            <form class="w-75" method="POST" action="{{ url('partida') }}">
                @csrf
                <div class="row mb-5">
                    <label for="numeroJugadores" class="col-sm-4 form-label fs-3">Numero de jugadores</label>
                    <div class="col-sm-8 my-auto">
                        <input type="number" class="form-control" id="numeroJugadores" name="numeroJugadores" placeholder="Numero de jugadores" min="1" max="100" required>
                    </div>
                </div>
                <div class="row mb-5">
                    <label for="jugadores" class="col-sm-4 form-label fs-3 my-auto">Nombres de los jugadores</label>
                    <div style="max-height: 20vh;" id="jugadores" class="col-sm-8 overflow-auto my-auto">
                        <input id="jugador0" name="jugador0" type="text" class="form-control my-3" required placeholder="Nombre">
                    </div>
                </div>
                <div class="row text-center mt-5">
                    <button type="submit" class="btn btn-primary mx-auto w-50">Empezar</button>
                </div>
            </form>
        </div>
    </body>
    <footer class="position-fixed top-100 start-50 translate-middle text-center pb-5">
        <p class="mb-5">Website provided by Isaac Aguilera Cano || Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) </p>
    </footer>
</html>