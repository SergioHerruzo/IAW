<?php
if ($_POST['enviar']) {
    $euros = floatval($_POST['moneda']);
    $dolares = $euros * 1.1;
    echo "$euros € = $dolares $";
}
?>
