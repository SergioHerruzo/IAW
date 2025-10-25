<?php
if($_POST['enviar'])
    $preu = $_POST['preu'];
    $iva = $_POST['iva'];
    $total = $preu + ($preu * $iva / 100);
    echo $total
?>