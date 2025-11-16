<?php
session_start();
if($_SERVER['REQUEST_METHOD']=='POST'){
$codi=trim($_POST['codi']);
$valid=false;
if($_SESSION['visites']>=5&&$_SESSION['visites']<10&&$codi=='BOTIGA20'&&!isset($_SESSION['descompte_usat']))$valid=true;
if($_SESSION['visites']>=10&&$codi=='BOTIGA50'&&!isset($_SESSION['descompte_usat']))$valid=true;
if($valid){$_SESSION['descompte_usat']=true;echo "Compra realitzada amb descompte";}else{echo "Codi invàlid o sense oferta";}
}else{echo "Accés no vàlid";}
?>