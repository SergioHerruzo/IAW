<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recipe_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Handle Comment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $comment = trim($_POST['comment_text']);
    if (!empty($comment)) {
        $stmt_c = $conn->prepare("INSERT INTO comments (recipe_id, user_id, text) VALUES (?, ?, ?)");
        $stmt_c->bind_param("iis", $recipe_id, $user_id, $comment);
        $stmt_c->execute();
        $stmt_c->close();
        header("Location: view_recipe.php?id=$recipe_id&msg=comentari_ok");
        exit();
    }
}

// Handle Comment Deletion
if (isset($_GET['delete_comment'])) {
    $comment_id = $_GET['delete_comment'];
    $stmt_d = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt_d->bind_param("ii", $comment_id, $user_id);
    $stmt_d->execute();
    $stmt_d->close();
    header("Location: view_recipe.php?id=$recipe_id&msg=comentari_esborrat");
    exit();
}

// Fetch Recipe
$stmt = $conn->prepare("SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recipe) {
    header("Location: error.php?msg=Recepta no trobada");
    exit();
}

// Fetch Ingredients
$stmt_i = $conn->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
$stmt_i->bind_param("i", $recipe_id);
$stmt_i->execute();
$ingredients = $stmt_i->get_result();
$stmt_i->close();

// Fetch Comments
$stmt_com = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = ? ORDER BY c.created_at DESC");
$stmt_com->bind_param("i", $recipe_id);
$stmt_com->execute();
$comments = $stmt_com->get_result();
$stmt_com->close();

$is_owner = ($recipe['user_id'] == $user_id);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div style="background-color: var(--card-bg); padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($recipe['title']); ?></h1>
                    <p style="color: var(--muted-text);">
                        Per <strong><?php echo htmlspecialchars($recipe['username']); ?></strong> | 
                        <?php echo $recipe['created_at']; ?> | 
                        <?php echo htmlspecialchars($recipe['category']); ?>
                    </p>
                </div>
                <?php if ($is_owner && $user_role === 'Cuiner'): ?>
                    <div>
                        <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-secondary">Editar</a>
                        <a href="delete_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-danger" onclick="return confirm('Segur que vols eliminar aquesta recepta?')">Eliminar</a>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin: 2rem 0;">
                <h3>Descripció</h3>
                <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($recipe['description']); ?></p>
            </div>

            <div style="margin: 2rem 0;">
                <h3>Ingredients</h3>
                <ul style="list-style-position: inside;">
                    <?php while($ing = $ingredients->fetch_assoc()): ?>
                        <li><strong><?php echo htmlspecialchars($ing['quantity']); ?></strong> <?php echo htmlspecialchars($ing['name']); ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Comments Section -->
        <div style="max-width: 800px; margin: 0 auto;">
            <h3>Comentaris</h3>
            
            <div class="form-container" style="margin: 1rem 0; padding: 1.5rem; max-width: 100%;">
                <form action="view_recipe.php?id=<?php echo $recipe_id; ?>" method="POST">
                    <div class="form-group">
                        <textarea name="comment_text" rows="3" placeholder="Escriu el teu comentari aquí..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Comentari</button>
                </form>
            </div>

            <?php if ($comments->num_rows > 0): ?>
                <?php while($com = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($com['username']); ?></strong>
                            <span><?php echo $com['created_at']; ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($com['text']); ?></p>
                        
                        <?php if ($com['user_id'] == $user_id): ?>
                            <div style="text-align: right; margin-top: 0.5rem;">
                                <a href="view_recipe.php?id=<?php echo $recipe_id; ?>&delete_comment=<?php echo $com['id']; ?>" style="color: var(--danger); font-size: 0.8rem;" onclick="return confirm('Esborrar comentari?')">Eliminar</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--muted-text);">Encara no hi ha comentaris. Sigues el primer!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
