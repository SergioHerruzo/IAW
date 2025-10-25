<?php
$hora = date('H');
$mins = date('i');
$sec = date ('s');
echo(date('H:i:s'));

if ($hora >= 5 && $hora < 14) {
    echo "Bon dia";
} elseif ($hora >= 14 && $hora < 19) {
    echo "Bona tarda";
} else {
    echo "Bona nit";
}
?>

