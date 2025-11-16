<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
$majoredat=$_POST['majoredat'];
$idioma=$_POST['idioma'];
$moneda=$_POST['moneda'];
setcookie('majoredat',$majoredat,time()+3600*24*30,'/');
setcookie('idioma',$idioma,time()+3600*24*30,'/');
setcookie('moneda',$moneda,time()+3600*24*30,'/');
}else{
$majoredat=$_COOKIE['majoredat']??'';
$idioma=$_COOKIE['idioma']??'ca';
$moneda=$_COOKIE['moneda']??'eur';
}
$preus=['eur'=>['terrasses'=>39,'priorat'=>55],'gbp'=>['terrasses'=>33,'priorat'=>47],'usd'=>['terrasses'=>42,'priorat'=>60]];
$simbols=['eur'=>'€','gbp'=>'£','usd'=>'$'];
$productes=['terrasses'=>['ca'=>'Les Terrasses','es'=>'Les Terrasses','en'=>'Les Terrasses'],
'priorat'=>['ca'=>'Priorat Reserva','es'=>'Priorat Reserva','en'=>'Priorat Reserve']];
$missatges=['ca'=>['menor'=>'No et podem vendre alcohol si ets menor d’edat.','oferta'=>'T’oferim el vi “%s” a un preu de %s %s'],
'es'=>['menor'=>'No te podemos vender alcohol si eres menor de edad.','oferta'=>'Te ofrecemos el vino “%s” a un precio de %s %s'],
'en'=>['menor'=>'We cannot sell you alcohol if you are under age.','oferta'=>'We offer you the wine “%s” at a price of %s %s']];
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Info</title></head><body>
<?php if($majoredat=='no'): ?>
<p><?php echo $missatges[$idioma]['menor']; ?></p>
<?php else: ?>
<p><?php printf($missatges[$idioma]['oferta'],$productes['terrasses'][$idioma],$preus[$moneda]['terrasses'],$simbols[$moneda]); ?></p>
<p><?php printf($missatges[$idioma]['oferta'],$productes['priorat'][$idioma],$preus[$moneda]['priorat'],$simbols[$moneda]); ?></p>
<?php endif; ?>
</body></html>