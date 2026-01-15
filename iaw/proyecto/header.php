<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Receptes</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">
        <a href="acces.php">🍲 ReceptesDelicades</a>
    </div>
    <nav>
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>Benvingut, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</li>
                <li><a href="acces.php">Inici</a></li>
                <?php if ($_SESSION['role'] === 'Cuiner'): ?>
                    <li><a href="create_recipe.php">Nova Recepta</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="auth-btn">Tancar Sessió</a></li>
            <?php else: ?>
                <li><a href="login.php">Iniciar Sessió</a></li>
                <li><a href="register.php" class="auth-btn">Registrar-se</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
