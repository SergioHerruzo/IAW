<?php 
$host="localhost";
$user="root";
$pass=""; 
$db ="bibliotech"; 
"mydb";

$conexion = mysqli_connect($host, $user, $pass, $db)
    or die("Problemas con la conexión");
echo "Conexión exitosa";

$tematica = $_POST['nom'];
$consulta = "SELECT l.titol, l.any_publicacio, a.nom, a.pais FROM llibres l JOIN autors a ON l.id_autor = a.id WHERE l.genere = '$tematica'";

$registros = mysqli_query($conexion, $consulta);
echo "<h2>Libros de este genero: $tematica</h2>";

if (mysqli_num_rows($registros) > 0) {

    echo "<table border='1'>";
    echo "<tr>
            <th>Títol</th>
            <th>Autor</th>
            <th>País</th>
            <th>Any Publicació</th>
          </tr>";
    $total = 0;
    $antic = null;
    $recent = null;

    while ($reg = mysqli_fetch_array($registros)) {
        echo "<tr>";
        echo "<td>" . $reg['titol'] . "</td>";
        echo "<td>" . $reg['nom'] . "</td>";
        echo "<td>" . $reg['pais'] . "</td>";
        echo "<td>" . $reg['any_publicacio'] . "</td>";
        echo "</tr>";
        $total = $total+1;
        
        $any = $reg['any_publicacio'];

        if ($antic === null || $any < $antic) { $antic = $any; }

        if ($recent === null || $any > $recent) { $recent = $any; }
    }
    echo "</table>";
    echo "<p>El nombre total de llibres trobats: $total</p>";
    echo "<p>L'any de publicació més antic: $antic i el més recent: $recent</p>";
} else {

    echo "No s'han trobat llibres del gènere indicat.";
}
mysqli_close($conexion);
?>