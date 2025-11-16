<?php
session_start();
if(!isset($_SESSION['visites']))$_SESSION['visites']=0;
$_SESSION['visites']++;
$visites=$_SESSION['visites'];
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Comptador</title></head><body>
<h2>Visites: <?php echo $visites; ?></h2>
</body></html>