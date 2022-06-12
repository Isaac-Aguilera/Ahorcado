// Actualizar el numero de nombres que se tienen que poner.
document.getElementById('numeroJugadores').addEventListener("change",function() {
    document.getElementById('jugadores').innerHTML = "";
    $jugadores = document.getElementById('numeroJugadores').value;
    // He limitado el numero a 100 para que no ponges Ej:1000000 y se colapse la pagina.
    if ($jugadores > 100) {$jugadores = 100;}
    for ($i = 0; $i < $jugadores; $i++) {
        document.getElementById('jugadores').innerHTML += '<input id="jugador'+$i+'" name="jugador'+$i+'" type="text" class="form-control my-3" required placeholder="Nombre">'; 
    }
});

// Imagen de superheroe aleatorio en el navbar.
document.getElementById('headerImage').src = "img/"+superheroeAleatorio();

// Funcion para elegir una imagen aleatoria de un superheroe.
function superheroeAleatorio() {
    var texto = "AngryHulk.png,Batman.png,capitanA.png,HappyHulk.png,IronMan.png,PencilThor.png,superhero1.png,superhero2.png,superhero3.png";
    opciones = texto.split(",");
    posicionAleatoria = Math.floor(Math.random() * opciones.length);
    return opciones[posicionAleatoria];
}