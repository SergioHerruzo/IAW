<?php
session_start();
if($_SERVER['REQUEST_METHOD']=='POST'){
$usuari=trim($_POST['usuari']);
$pwd=trim($_POST['pwd']);
if($usuari==$pwd&&$usuari!=''){
$_SESSION['usuari']=$usuari;
header('Location: pagina1.php');
exit;
}
}
header('Location: index.html');
exit;
?>