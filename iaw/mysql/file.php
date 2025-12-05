<?php

// 1. Conexión con manejo de errores moderno
$con = mysqli_connect("localhost", "root", "", "mibd");

if (mysqli_connect_errno()) {
    // Usar mysqli_connect_error() para errores de conexión
    die("Error en la conexión a MySQL: " . mysqli_connect_error());
}

// 2. Consulta SQL
$SQL = "SELECT id, name FROM mitabla;";
$registros = mysqli_query($con, $SQL);

if (!$registros) {
    // Usar mysqli_error($con) para errores de consulta
    die("Error en la consulta: " . mysqli_error($con));
}

// 3. Mostrar resultados en una tabla
echo "<table border='1'>"; 
echo "<tr><th>ID</th><th>Nombre</th></tr>"; // Uso <th> y cierro las etiquetas

while($registro = mysqli_fetch_row($registros) ) {
    // Se corrigen las etiquetas HTML: <td> y </td>
    echo "<tr><td>" . htmlspecialchars($registro[0]) . "</td><td>" . htmlspecialchars($registro[1]) . "</td></tr>";
}

echo "</table>";

// 4. Cerrar la conexión
mysqli_close($con);

?>