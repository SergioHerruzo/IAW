<?php
session_start();
require_once 'db_connect.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$msg = $_GET['msg'] ?? '';

// Lógica de búsqueda y ordenación
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'rating'; // Ordenación por defecto por valoración

$sql = "SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    // Buscar por título, categoría o ingrediente (requiere unión)
    // Para buscar ingredientes necesitamos una subconsulta o unión. Hacemos left join para filtrar por ingredientes.
    // Realmente más simple: SELECT DISTINCT r.* from recipes r ...
    
    $search_term = "%$search%";
    $sql = "SELECT DISTINCT r.*, u.username FROM recipes r 
            JOIN users u ON r.user_id = u.id 
            LEFT JOIN ingredients i ON r.id = i.recipe_id 
            WHERE (r.title LIKE ? OR r.category LIKE ? OR i.name LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

// Ordenar por
if ($sort === 'date') {
    $sql .= " ORDER BY r.created_at DESC";
} else {
    $sql .= " ORDER BY r.rating DESC, r.created_at DESC"; // Valoración primaria, fecha secundaria
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Inici - ReceptesDelicades</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Receptes Destacades</h1>
            
            <?php if ($user_role === 'Cuiner'): ?>
                <a href="create_recipe.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Recepta</a>
            <?php endif; ?>
        </div>

        <!-- Buscar y Filtrar -->
        <div style="background-color: var(--card-bg); padding: 1rem; border-radius: 10px; margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <form action="acces.php" method="GET" style="display: flex; gap: 1rem; flex: 1; align-items: center;">
                <input type="text" name="search" placeholder="Cercar per nom, categoria o ingredient..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1;">
                <select name="sort" onchange="this.form.submit()" style="width: auto;">
                    <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Millor Valorades</option>
                    <option value="date" <?php echo $sort === 'date' ? 'selected' : ''; ?>>Més Recents</option>
                </select>
                <button type="submit" class="btn btn-secondary">Cercar</button>
                <?php if ($search): ?>
                    <a href="acces.php" class="btn btn-danger">Netejar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($msg): ?>
            <div style="background-color: var(--success); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="recipe-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="recipe-card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="recipe-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['username']); ?></span>
                            <span><i class="fas fa-star" style="color: gold;"></i> <?php echo $row['rating']; ?>/5</span>
                        </div>
                        <p style="color: var(--muted-text); margin-bottom: 1rem;">
                            <?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="background-color: #0f3460; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;"><?php echo htmlspecialchars($row['category']); ?></span>
                            <a href="view_recipe.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Veure més</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No s'han trobat receptes.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
