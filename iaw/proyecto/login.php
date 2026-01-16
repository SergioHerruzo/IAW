<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['user_id'])) {
    header("Location: acces.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Tots els camps són obligatoris.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invàlid.";
    } else {
        // Validar credenciales
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();

            // Verificar contraseña
            if (password_verify($password, $hashed_password)) {
                // Iniciar sesión
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: acces.php");
                exit();
            } else {
                $error = "Contrasenya incorrecta.";
            }
        } else {
            $error = "No existeix cap usuari amb aquest email.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sessió - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; color: var(--primary-color);">Iniciar Sessió</h2>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contrasenya</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Entrar</button>
            </form>
            <p style="text-align: center; margin-top: 1rem; color: var(--muted-text);">
                No tens compte? <a href="register.php">Registra't</a>
            </p>
        </div>
    </div>
</body>
</html>
