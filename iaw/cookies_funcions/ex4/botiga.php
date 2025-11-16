<?php
session_start();
if($_SERVER['REQUEST_METHOD']=='POST'){
$producte=$_POST['producte'];
$preu=(float)$_POST['preu'];
$quantitat=(int)$_POST['quantitat'];
if($preu>0&&$quantitat>0){
$_SESSION['cistella'][]=['producte'=>$producte,'preu'=>$preu,'quantitat'=>$quantitat];
}
}
header('Location: index.html');
exit;
?>