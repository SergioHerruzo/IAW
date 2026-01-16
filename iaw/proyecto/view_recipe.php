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

// Gestionar envío de valoraciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    if ($rating >= 1 && $rating <= 5) {
        $stmt_r = $conn->prepare("INSERT INTO ratings (recipe_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?");
        $stmt_r->bind_param("iiii", $recipe_id, $user_id, $rating, $rating);
        $stmt_r->execute();
        $stmt_r->close();

        // Actualizar valoración media en la tabla de recetas
        $stmt_avg = $conn->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE recipe_id = ?");
        $stmt_avg->bind_param("i", $recipe_id);
        $stmt_avg->execute();
        $res_avg = $stmt_avg->get_result()->fetch_assoc();
        $new_avg = $res_avg['avg_rating'] ?? 0;
        $stmt_avg->close();

        $stmt_upd = $conn->prepare("UPDATE recipes SET rating = ? WHERE id = ?");
        $stmt_upd->bind_param("di", $new_avg, $recipe_id);
        $stmt_upd->execute();
        $stmt_upd->close();

        header("Location: view_recipe.php?id=$recipe_id&msg=valoracio_ok");
        exit();
    }
}

// Gestionar envío de comentarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $comment = trim($_POST['comment_text']);
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : NULL;
    
    if (!empty($comment)) {
        $stmt_c = $conn->prepare("INSERT INTO comments (recipe_id, user_id, text, parent_id) VALUES (?, ?, ?, ?)");
        $stmt_c->bind_param("iisi", $recipe_id, $user_id, $comment, $parent_id);
        $stmt_c->execute();
        $stmt_c->close();
        header("Location: view_recipe.php?id=$recipe_id&msg=comentari_ok");
        exit();
    }
}

// Gestionar eliminación de comentarios
if (isset($_GET['delete_comment'])) {
    $comment_id = $_GET['delete_comment'];
    // Permitir al autor borrar cualquier comentario, o al usuario borrar el suyo propio
    // Obtener propietario de la receta primero
    $stmt_check = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
    $stmt_check->bind_param("i", $recipe_id);
    $stmt_check->execute();
    $r_owner = $stmt_check->get_result()->fetch_assoc();
    $stmt_check->close();

    $is_recipe_owner = ($r_owner['user_id'] == $user_id);

    if ($is_recipe_owner) {
         $stmt_d = $conn->prepare("DELETE FROM comments WHERE id = ?");
         $stmt_d->bind_param("i", $comment_id);
    } else {
         $stmt_d = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
         $stmt_d->bind_param("ii", $comment_id, $user_id);
    }
    
    $stmt_d->execute();
    $stmt_d->close();
    header("Location: view_recipe.php?id=$recipe_id&msg=comentari_esborrat");
    exit();
}

// Obtener receta
$stmt = $conn->prepare("SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recipe) {
    header("Location: error.php?msg=Recepta no trobada");
    exit();
}

// Obtener ingredientes
$stmt_i = $conn->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
$stmt_i->bind_param("i", $recipe_id);
$stmt_i->execute();
$ingredients = $stmt_i->get_result();
$stmt_i->close();

// Obtener comentarios
$stmt_com = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = ? ORDER BY c.created_at ASC");
$stmt_com->bind_param("i", $recipe_id);
$stmt_com->execute();
$all_comments = $stmt_com->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_com->close();

// Organizar comentarios en árbol
$comments_tree = [];
$comments_map = [];
foreach ($all_comments as $c) {
    $c['replies'] = [];
    $comments_map[$c['id']] = $c;
}
foreach ($comments_map as $id => &$c) {
    if ($c['parent_id']) {
        if (isset($comments_map[$c['parent_id']])) {
            $comments_map[$c['parent_id']]['replies'][] = &$c;
        }
    } else {
        $comments_tree[] = &$c;
    }
}
unset($c);

// Obtener valoración del usuario
$user_rating = 0;
$stmt_ur = $conn->prepare("SELECT rating FROM ratings WHERE recipe_id = ? AND user_id = ?");
$stmt_ur->bind_param("ii", $recipe_id, $user_id);
$stmt_ur->execute();
$res_ur = $stmt_ur->get_result()->fetch_assoc();
if ($res_ur) {
    $user_rating = $res_ur['rating'];
}
$stmt_ur->close();

$is_owner = ($recipe['user_id'] == $user_id);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
    <script>
        function toggleReplyForm(commentId) {
            var form = document.getElementById('reply-form-' + commentId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div style="background-color: var(--card-bg); padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1 style="color: var(--primary-color); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem;">
                        <?php echo htmlspecialchars($recipe['title']); ?>
                        <span class="rating-display">
                            <?php 
                            $stars = round($recipe['rating']);
                            echo str_repeat("★", $stars) . str_repeat("☆", 5 - $stars); 
                            ?> 
                            <span style="font-size: 0.6em; color: var(--muted-text);"> (<?php echo number_format($recipe['rating'], 1); ?>)</span>
                        </span>
                    </h1>
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
            
            <!-- Sección de valoración -->
            <div style="margin-top: 2rem; border-top: 1px solid #333; padding-top: 1rem;">
                 <?php if ($user_rating > 0): ?>
                    <p>Has valorat aquesta recepta amb <strong><?php echo $user_rating; ?> estrelles</strong>.</p>
                 <?php elseif ($user_role === 'Visitant' || (!$is_owner)): ?>
                    <h4>Valora aquesta recepta de esquerra a dreta tenin en compte que la esquerra és la més baixa i la dreta la més alta:</h4>
                    <form action="view_recipe.php?id=<?php echo $recipe_id; ?>" method="POST" class="rating-form">
                        <input type="radio" id="star5" name="rating" value="1">
                        <input type="radio" id="star4" name="rating" value="2">
                        <input type="radio" id="star3" name="rating" value="3">
                        <input type="radio" id="star2" name="rating" value="4">
                        <input type="radio" id="star1" name="rating" value="5">
                    </form>
                 <?php endif; ?>
            </div>
        </div>

        <!-- Sección de comentarios -->
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

            <?php if (count($comments_tree) > 0): ?>
                <?php 
                function render_comment($com, $user_id, $recipe_id, $is_owner) {
                    $is_author = ($com['user_id'] == $user_id);
                    $reply_class = $com['parent_id'] ? 'reply' : '';
                    ?>
                    <div class="comment <?php echo $reply_class; ?>">
                        <div class="comment-header">
                            <strong><?php echo htmlspecialchars($com['username']); ?></strong>
                            <span><?php echo $com['created_at']; ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($com['text']); ?></p>
                        
                        <div style="display: flex; gap: 1rem; justify-content: flex-end; align-items: center; margin-top: 0.5rem;">
                            <!-- Botón de respuesta para el propietario de la receta -->
                            <?php if ($is_owner && !$com['parent_id']): ?>
                                <button class="btn btn-sm btn-secondary" onclick="toggleReplyForm(<?php echo $com['id']; ?>)">Respondre</button>
                            <?php endif; ?>

                            <!-- Botón de eliminar -->
                            <?php if ($is_author || $is_owner): ?>
                                <a href="view_recipe.php?id=<?php echo $recipe_id; ?>&delete_comment=<?php echo $com['id']; ?>" style="color: var(--danger); font-size: 0.8rem;" onclick="return confirm('Esborrar comentari?')">Eliminar</a>
                            <?php endif; ?>
                        </div>

                        <!-- Formulario de respuesta -->
                        <div id="reply-form-<?php echo $com['id']; ?>" style="display: none; margin-top: 1rem;">
                            <form action="view_recipe.php?id=<?php echo $recipe_id; ?>" method="POST">
                                <input type="hidden" name="parent_id" value="<?php echo $com['id']; ?>">
                                <div class="form-group">
                                    <textarea name="comment_text" rows="2" placeholder="La teva resposta..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Enviar Resposta</button>
                            </form>
                        </div>
                    <!-- Respuestas anidadas -->
                     <?php if (!empty($com['replies'])): ?>
                        <div style="margin-top: 1rem; padding-left: 1rem; border-left: 2px solid rgba(255,255,255,0.1);">
                            <?php foreach($com['replies'] as $reply): ?>
                                <?php render_comment($reply, $user_id, $recipe_id, $is_owner); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php } ?>

                <?php foreach($comments_tree as $com): ?>
                    <?php render_comment($com, $user_id, $recipe_id, $is_owner); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--muted-text);">Encara no hi ha comentaris. Sigues el primer!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
