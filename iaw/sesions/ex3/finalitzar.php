<?php
session_start();
$cistella=$_SESSION['cistella']??[];
$preus=['p1'=>39.00,'p2'=>55.00];
$noms=['p1'=>'Vi Les Terrasses','p2'=>'Vi Priorat Reserva'];
$total=0;
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Resum</title></head><body>
<h2>Resum compra</h2>
<?php if(empty($cistella)): ?>
<p>Cistella buida</p>
<?php else: ?>
<table border="1"><tr><th>Producte</th><th>Quantitat</th><th>Preu unitari</th><th>Subtotal</th></tr>
<?php foreach($cistella as $id=>$q):
$sub=$q*$preus[$id];
$total+=$sub;
?>
<tr><td><?php echo $noms[$id]; ?></td><td><?php echo $q; ?></td><td><?php echo $preus[$id]; ?> €</td><td><?php echo $sub; ?> €</td></tr>
<?php endforeach; ?>
<tr><td colspan="3">Total</td><td><?php echo $total; ?> €</td></tr>
</table>
<form action="" method="post"><input type="submit" name="confirm" value="Confirmar compra"></form>
<?php endif; ?>
<a href="index.html">Tornar</a>
<?php
if(isset($_POST['confirm'])){
unset($_SESSION['cistella']);
echo "<p>Compra confirmada</p>";
}
?>
</body></html>