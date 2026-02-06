<?php // database configuration
$host="localhost";//Servidor local
$user="root"; //Usuario mysql
$pass=""; //Contraseña
$db ="generalexam"; 
"mydb";

$conexion = mysqli_connect($host, $user, $pass, $db)
    or die("Problemas con la conexión");
echo "Conexión exitosa"; // Opcional, para probar

$puntuacion_buscada = $_POST['nom'];
$consulta = "SELECT * FROM peliculas WHERE puntuacion = '$puntuacion_buscada'";
$registros = mysqli_query($conexion, $consulta);
echo "<h2>Películas con puntuación: $puntuacion_buscada</h2>";

// Usamos un bucle para recorrer cada fila que encontró la base de datos
while ($reg = mysqli_fetch_array($registros)) {
    echo "TITULO " . $reg['TITULO'] . "<br>";
    echo "<hr>";
}

// Si no encontró nada
if (mysqli_num_rows($registros) == 0) {
    echo "No se encontraron películas con esa puntuación.";
}
?>