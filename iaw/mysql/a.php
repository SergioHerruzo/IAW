<?php

$a = "Nicolas";
$b = "Nicolas@iticbcn.cat";
$c = "IC01";

$conexion = mysqli_connect("localhost", "root", "", "alumnos")
    or die("Problemas con la conexión");

$query = "insert into alumnos(nombre, email, codigocurso) value ('$a', '$b', '$c')";

mysqli_query($conexion, $query)
    or die("Problemas en el insert".mysqli_error($conexion));

mysqli_close($conexion);

echo "el alumno fue dado de alta.";

?>