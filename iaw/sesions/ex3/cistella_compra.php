<?php
session_start();
$preus=['p1'=>39.00,'p2'=>55.00];
$noms=['p1'=>'Vi Les Terrasses','p2'=>'Vi Priorat Reserva'];
if($_SERVER['REQUEST_METHOD']=='POST'){
if(isset($_POST['add1'])){
$q=(int)$_POST['q1'];
if($q>0)$_SESSION['cistella']['p1']=($_SESSION['cistella']['p1']??0)+$q;
}
if(isset($_POST['add2'])){
$q=(int)$_POST['q2'];
if($q>0)$_SESSION['cistella']['p2']=($_SESSION['cistella']['p2']??0)+$q;
}
}
header('Location: index.html');
exit;
?>