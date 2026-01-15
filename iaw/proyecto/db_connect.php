<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$db_name = 'recipe_platform';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists (redundant if using db.sql manually, but good for auto-setup feel)
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($db_name);
} else {
    die("Error creating database: " . $conn->error);
}

// Charset
$conn->set_charset("utf8mb4");
?>
