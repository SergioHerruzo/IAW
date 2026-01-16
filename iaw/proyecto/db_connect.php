<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // La contraseña por defecto de XAMPP está vacía
$db_name = 'recipe_platform';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($db_name);
} else {
    die("Error creating database: " . $conn->error);
}

// Configurar conjunto de caracteres
$conn->set_charset("utf8mb4");
?>
