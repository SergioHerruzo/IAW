<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: acces.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validations
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Tots els camps són obligatoris.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invàlid.";
    } elseif ($password !== $confirm_password) {
        $error = "Les contrasenyes no coincideixen.";
    } elseif (!in_array($role, ['Cuiner', 'Visitant'])) {
        $error = "Rol invàlid.";
    } else {
        // Check duplicate email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $error = "Aquest email ja està registrat.";
        } else {
            // Register user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                header("Location: login.php?msg=registre_ok");
                exit();
            } else {
                $error = "Error al registrar: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Registre - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; color: var(--primary-color);">Crear Compte</h2>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'Usuari</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Contrasenya</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contrasenya</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="role">Tipus d'Usuari</label>
                    <select id="role" name="role">
                        <option value="Visitant">Visitant (Cercar i Comentar)</option>
                        <option value="Cuiner">Cuiner (Publicar Receptes)</option>
                    </select>
                </div>
                <button type="submit">Registrar-se</button>
            </form>
            <p style="text-align: center; margin-top: 1rem; color: var(--muted-text);">
                Ja tens compte? <a href="login.php">Inicia Sessió</a>
            </p>
        </div>
    </div>
</body>
</html>
