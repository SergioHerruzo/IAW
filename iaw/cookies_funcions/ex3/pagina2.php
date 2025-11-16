<?php
session_start();
if(!isset($_SESSION['usuari'])){header('Location: index.html');exit;}
$usuari=$_SESSION['usuari'];
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Pàgina 2</title></head><body>
<h2>Usuari: <?php echo $usuari; ?> <a href="logout.php">Desconnectar</a></h2>
<p>Informació de la pàgina 2</p>
<a href="pagina1.php">Anar a pàgina 1</a>
</body></html>