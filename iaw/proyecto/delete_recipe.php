<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cuiner') {
    header("Location: acces.php");
    exit();
}

$recipe_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Check ownership
$stmt = $conn->prepare("SELECT id FROM recipes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
if ($stmt->num_rows > 0) {
    $stmt->close();
    // Delete
    // Because of ON DELETE CASCADE in SQL, ingredients and comments should be deleted automatically.
    // However, explicitly for safety:
    $stmt_del = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt_del->bind_param("i", $recipe_id);
    if ($stmt_del->execute()) {
        header("Location: acces.php?msg=recepta_esborrada");
    } else {
        header("Location: error.php?msg=Error al esborrar");
    }
} else {
    header("Location: error.php?msg=No tens permís");
}
exit();
?>
