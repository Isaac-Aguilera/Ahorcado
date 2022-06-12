<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script>var token = "{{ csrf_token() }}";var id = {{ $jugador->id }}</script>
        <script src="{{ asset('js/partida.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <header>
        <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
            <div class="container-fluid">
              <h2 id="nombre" class="navbar-brand fs-1">
                Jugador: {{ $jugador->nombre }}
              </h2>
              <img id="headerImage" src="" style="height: 6vh;" class="img-fluid" alt="">
            </div>
          </nav>
    </header>
    <body class="antialiased">
        <h1 class="fs-1 text-center mt-5">Ahorcado de superheroes</h1>
        <div class="row align-items-center justify-content-center mt-5">
            <div class="col-6">
                <canvas id="canvas"></canvas>
                <div id="ahorcado"></div>
            </div>
            <div id="palabra" class="col-6">
                <h2 class="fs-1 mt-5 text-center">{{ $jugador->palabra }}</h2>
            </div>
        </div>
        <div class="mt-5">
            <h3 class="text-center fs-2">Letras falladas</h3>
            <div id="fallos" class="row align-items-center justify-content-center gx-5 ">
            </div>
        </div>
        <div id="modal"></div>
    </body>
    <footer class="position-sticky bottom-0 text-center">
        <p>Website provided by Isaac Aguilera Cano || Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) </p>
    </footer>
</html>