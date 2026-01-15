<?php
session_start();
require_once 'header.php';
?>
<div class="container">
    <div class="form-container" style="text-align: center;">
        <h2 style="color: var(--danger);">Ha ocorregut un error</h2>
        <?php
        $error = $_GET['msg'] ?? 'Error desconegut';
        $type = $_GET['type'] ?? 'general';
        
        // Map types to friendly messages if needed, or just show the msg
        echo "<p class='error-msg'>" . htmlspecialchars($error) . "</p>";
        ?>
        <a href="javascript:history.back()" class="btn btn-secondary">Tornar enrere</a>
        <a href="index.html" class="btn btn-primary">Anar a l'inici</a>
    </div>
</div>
</body>
</html>
