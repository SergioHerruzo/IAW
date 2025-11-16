<?php
session_start();
if(!isset($_SESSION['visites']))$_SESSION['visites']=0;
$_SESSION['visites']++;
$visites=$_SESSION['visites'];
$mostra20=$visites>=5&&$visites<10&&!isset($_SESSION['descompte_usat']);
$mostra50=$visites>=10&&!isset($_SESSION['descompte_usat']);
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Comptador</title></head><body>
<h2>Visites: <?php echo $visites; ?></h2>
<?php if($mostra20): ?>
<p>Oferta exclusiva! Utilitza el codi BOTIGA20 per obtenir un 20% de descompte en les teves primeres compres a la botiga</p>
<?php endif; ?>
<?php if($mostra50): ?>
<p>Oferta exclusiva sols per a tu! Utilitza el codi BOTIGA50 per obtenir un 50% de descompte en les teves primeres compres a la botiga</p>
<?php endif; ?>
<form action="file.php" method="post" onsubmit="return validar()">
<label>Codi descompte: <input type="text" name="codi" id="codi"></label><br>
<input type="submit" value="Compra">
</form>
<script>
function validar(){var c=document.getElementById('codi').value.trim();<?php if($mostra20): ?>if(c!='BOTIGA20')return false;<?php elseif($mostra50): ?>if(c!='BOTIGA50')return false;<?php else: ?>if(c!='')return false;<?php endif; ?>return true;}
</script>
</body></html>