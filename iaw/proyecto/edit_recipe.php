<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Cuiner') {
    header("Location: acces.php");
    exit();
}

$recipe_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
$error = '';

// Obtener receta existente
$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recipe) {
    header("Location: error.php?msg=Recepta no trobada o no tens permís");
    exit();
}

// Obtener ingredientes
$stmt_i = $conn->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
$stmt_i->bind_param("i", $recipe_id);
$stmt_i->execute();
$ingredients = $stmt_i->get_result();
$ing_data = [];
while ($row = $ingredients->fetch_assoc()) {
    $ing_data[] = $row;
}
$stmt_i->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $ingredients_names = $_POST['ingredient_name'] ?? [];
    $ingredients_qtys = $_POST['ingredient_qty'] ?? [];

    if (empty($title) || empty($description) || empty($category)) {
        $error = "Tots els camps principals són obligatoris.";
    } else {
        $conn->begin_transaction();
        try {
            // Actualizar receta
            $stmt = $conn->prepare("UPDATE recipes SET title = ?, description = ?, category = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $category, $recipe_id);
            $stmt->execute();
            $stmt->close();

            // Reemplazar ingredientes (Borrar todos y reinsertar)
            // Esto es más simple que rastrear IDs para actualizaciones
            $stmt_del = $conn->prepare("DELETE FROM ingredients WHERE recipe_id = ?");
            $stmt_del->bind_param("i", $recipe_id);
            $stmt_del->execute();
            $stmt_del->close();

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
            header("Location: view_recipe.php?id=$recipe_id&msg=editat_ok");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al editar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar Recepta - ReceptesDelicades</title>
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
            <h2>Editar Recepta</h2>
            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="edit_recipe.php?id=<?php echo $recipe_id; ?>" method="POST">
                <div class="form-group">
                    <label for="title">Títol de la Recepta</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Categoria</label>
                    <select id="category" name="category">
                        <?php 
                        $cats = ['Entrant', 'Plat Principal', 'Postres', 'Beguda'];
                        foreach ($cats as $c) {
                            $sel = ($recipe['category'] === $c) ? 'selected' : '';
                            echo "<option value='$c' $sel>$c</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Descripció</label>
                    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                </div>

                <h3>Ingredients</h3>
                <div id="ingredients-container">
                    <?php foreach ($ing_data as $ing): ?>
                        <div class="ingredient-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="ingredient_name[]" value="<?php echo htmlspecialchars($ing['name']); ?>" placeholder="Nom ingredient" required style="flex: 2;">
                            <input type="text" name="ingredient_qty[]" value="<?php echo htmlspecialchars($ing['quantity']); ?>" placeholder="Quantitat" required style="flex: 1;">
                            <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger" style="padding: 0 10px;">X</button>
                        </div>
                    <?php endforeach; ?>
                    <!-- Si no hay ingredientes, mostrar una fila vacía -->
                    <?php if (empty($ing_data)): ?>
                        <div class="ingredient-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="ingredient_name[]" placeholder="Nom ingredient" required style="flex: 2;">
                            <input type="text" name="ingredient_qty[]" placeholder="Quantitat" required style="flex: 1;">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="addIngredient()" class="btn btn-secondary" style="margin-bottom: 2rem;">+ Afegir Ingredient</button>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Guardar Canvis</button>
                    <a href="view_recipe.php?id=<?php echo $recipe_id; ?>" class="btn btn-secondary" style="text-align: center; text-decoration: none; padding: 12px; display: block; flex: 1; box-sizing: border-box;">Cancel·lar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
