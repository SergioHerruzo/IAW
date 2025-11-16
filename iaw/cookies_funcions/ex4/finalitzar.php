<?php
session_start();
$cistella=$_SESSION['cistella']??[];
$total=0;
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Resum</title></head><body>
<h2>Resum de la compra</h2>
<?php if(empty($cistella)): ?>
<p>No hi ha productes</p>
<?php else: ?>
<table border="1"><tr><th>Producte</th><th>Preu</th><th>Quantitat</th><th>Subtotal</th></tr>
<?php foreach($cistella as $item):
$subtotal=$item['preu']*$item['quantitat'];
$total+=$subtotal;
?>
<tr><td><?php echo $item['producte']; ?></td><td><?php echo $item['preu']; ?></td><td><?php echo $item['quantitat']; ?></td><td><?php echo $subtotal; ?></td></tr>
<?php endforeach; ?>
<tr><td colspan="3">Total</td><td><?php echo $total; ?></td></tr>
</table>
<form action="" method="post"><input type="submit" name="confirmar" value="Confirmar compra"></form>
<?php endif; ?>
<a href="index.html">Tornar</a>
<?php
if(isset($_POST['confirmar'])){
unset($_SESSION['cistella']);
echo "<p>Compra confirmada</p>";
}
?>
</body></html>