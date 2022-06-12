// Crear canvas para la horca.
let canvas = document.getElementById('canvas');
let ctx = canvas.getContext('2d');
canvas.width=320;
canvas.height=320;
ctx.scale(20, 20);
ctx.clearRect(0, 0, canvas.width, canvas.height);
ctx.fillStyle = '#d95d39';

// Imagen de superheroe aleatorio en el navbar.
document.getElementById('headerImage').src = "img/"+superheroeAleatorio();

// Funcion para elegir una imagen aleatoria de un superheroe.
function superheroeAleatorio() {
    var texto = "AngryHulk.png,Batman.png,capitanA.png,HappyHulk.png,IronMan.png,PencilThor.png,superhero1.png,superhero2.png,superhero3.png";
    opciones = texto.split(",");
    posicionAleatoria = Math.floor(Math.random() * opciones.length);
    return opciones[posicionAleatoria];
}

// Funcion para volver a jugar con los mismos jugadores.
function reset() {
    // Lanza peticion ajax para volver a jugar con los mismos jugadores
    $.ajax({
        url: '/reset',
        method: 'post',
        data: {
            '_token': token,
        },
        error: function (response) {
            console.log(response);
        },
        success: function (response) {
            /*
                Si la peticion ajax funciona correctamente se muestra el nuevo contenido.
            */
            id = response['id'];
            // Actualizar el nombre.
            document.getElementById('nombre').innerHTML = 'Jugador: '+response['nombre'];

            // Actualizar las letras falladas.
            document.getElementById('fallos').innerHTML = '';
            response['fallos'].forEach(letra => {
                document.getElementById('fallos').innerHTML += "<p class='col-2 fs-1'>"+letra+"</p>"
            });

            // Actualizar palabra.
            document.getElementById('palabra').innerHTML = '<h2 class="fs-1 mt-5 text-center">'+response['palabra']+"</h2>";

            // Actualizar la horca
            document.getElementById('ahorcado').innerHTML = '';
            let canvas = document.getElementById('canvas');
            let ctx = canvas.getContext('2d');
            canvas.width=320;
            canvas.height=320;
            ctx.scale(20, 20);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#d95d39';
        }
    });
}

// Peticion ajax para jugar, se lanza al presionar una letra del teclado.
document.addEventListener('keyup', (event) => {
    if(event.keyCode >= 65 && event.keyCode <= 90){
        $.ajax({
            url: '/jugar',
            method: 'post',
            data: {
                '_token': token,
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
                // Actualizar el nombre.
                document.getElementById('nombre').innerHTML = 'Jugador: '+response['nombre'];

                // Actualizar las letras falladas.
                document.getElementById('fallos').innerHTML = '';
                response['fallos'].forEach(letra => {
                    document.getElementById('fallos').innerHTML += "<p class='col-2 fs-3'>"+letra+"</p>"
                });

                // Actualizar palabra.
                document.getElementById('palabra').innerHTML = '<h2 class="fs-1 mt-5 text-center">'+response['palabra']+"</h2>";

                // Actualizar la horca en funcion de las vidas restantes.
                if (response.vidas == 5) {
                    // Base
                    ctx.fillRect(1.5, 15, 8, 1);
                } else if(response.vidas == 4) {
                    // Palo
                    ctx.fillRect(4, 0, 1, 16);
                } else if(response.vidas == 3) {
                    // Derecha
                    ctx.fillRect(4, 0, 6, 1);
                } else if(response.vidas == 2) {
                    // Cuerda
                    ctx.fillRect(9, 1, 1, 2);
                } else if(response.vidas == 1) {
                    // Cara
                    document.getElementById('ahorcado').innerHTML = '<img src="img/BatmanFace.png" style="position:absolute;left:143px;top:250px;height:90px;" alt="Cara">';
                } else if(response.vidas == 0) {
                    // Cuerpo
                    document.getElementById('ahorcado').innerHTML = '<img src="img/Batman.png" style="position:absolute;left:135px;top:250px;height:150px;" alt="Cara">';
                }

                // Lanzar Pop-Up si algun jugador ha ganado.
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
                                <button type="button" onclick="window.location='/'" class="btn btn-secondary">Nuevos jugadores</button>
                                <button type="button" onclick="reset();" data-bs-dismiss="modal" class="btn btn-primary">Seguir los mismos jugadores</button>
                            </div>
                            </div>
                        </div>
                    </div>`;
                    new bootstrap.Modal($('#staticBackdrop')).show();
                } 

                // Lanzar Pop-Up si algun jugador ha perdido.
                else if (response['perdedor']) {
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
                                <button type="button" onclick="window.location='/'" class="btn btn-secondary">Nuevos jugadores</button>
                                <button type="button" data-bs-dismiss="modal" class="btn btn-primary">Seguir jugando</button>
                            </div>
                            </div>
                        </div>
                    </div>`;
                    new bootstrap.Modal($('#staticBackdrop')).show();
                } 
                
                // Lanzar Pop-Up si todos los jugadores han perdido.
                else if (response['fin']) {
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
                                <button type="button" onclick="window.location='/'" class="btn btn-secondary">Nuevos jugadores</button>
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