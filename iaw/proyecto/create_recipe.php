<?php
session_start();
require_once 'db_connect.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cuiner') {
    header("Location: acces.php"); // O página de error
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    // Los ingredientes se pasan como arrays
    $ingredients_names = $_POST['ingredient_name'] ?? [];
    $ingredients_qtys = $_POST['ingredient_qty'] ?? [];

    if (empty($title) || empty($description) || empty($category)) {
        $error = "Tots els camps principals són obligatoris.";
    } else {
        $user_id = $_SESSION['user_id'];
        
        // Transacción para asegurar que receta e ingredientes se añaden juntos
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, description, category, rating) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("isss", $user_id, $title, $description, $category);
            $stmt->execute();
            $recipe_id = $conn->insert_id;
            $stmt->close();

            // Añadir ingredientes
            if (!empty($ingredients_names)) {
                $stmt_ing = $conn->prepare("INSERT INTO ingredients (recipe_id, name, quantity) VALUES (?, ?, ?)");
                for ($i = 0; $i < count($ingredients_names); $i++) {
                    $ing_name = trim($ingredients_names[$i]);
                    $ing_qty = trim($ingredients_qtys[$i]);
                    if (!empty($ing_name)) {
                        $stmt_ing->bind_param("iss", $recipe_id, $ing_name, $ing_qty);
                        $stmt_ing->execute();
                    }
                }
                $stmt_ing->close();
            }

            $conn->commit();
            header("Location: acces.php?msg=recepta_creada");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al crear la recepta: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Nova Recepta - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
    <script>
        function addIngredient() {
            const container = document.getElementById('ingredients-container');
            const div = document.createElement('div');
            div.className = 'ingredient-row';
            div.style.display = 'flex';
            div.style.gap = '10px';
            div.style.marginBottom = '10px';
            div.innerHTML = `
                <input type="text" name="ingredient_name[]" placeholder="Nom ingredient" required style="flex: 2;">
                <input type="text" name="ingredient_qty[]" placeholder="Quantitat" required style="flex: 1;">
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger" style="padding: 0 10px;">X</button>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="form-container" style="max-width: 800px;">
            <h2>Nova Recepta</h2>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="create_recipe.php" method="POST">
                <div class="form-group">
                    <label for="title">Títol de la Recepta</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="category">Categoria</label>
                    <select id="category" name="category">
                        <option value="Entrant">Entrant</option>
                        <option value="Plat Principal">Plat Principal</option>
                        <option value="Postres">Postres</option>
                        <option value="Beguda">Beguda</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Descripció</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>

                <h3>Ingredients</h3>
                <div id="ingredients-container">
                    <div class="ingredient-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="ingredient_name[]" placeholder="Nom ingredient" required style="flex: 2;">
                        <input type="text" name="ingredient_qty[]" placeholder="Quantitat" required style="flex: 1;">
                    </div>
                </div>
                <button type="button" onclick="addIngredient()" class="btn btn-secondary" style="margin-bottom: 2rem;">+ Afegir Ingredient</button>

                <button type="submit" class="btn btn-primary" style="font-size: 1.2rem;">Publicar Recepta</button>
            </form>
        </div>
    </div>
</body>
</html>
