<?php
if($_POST['enviar']){
    $nom = ($_POST['nom']);
    $cognom = ($_POST['cognom']);
    $correu = ($_POST['Correu']);
    $missatge = ($_POST['Contacte']);

    echo "Missatge rebut, $nom. Gràcies per contactar. Et respondrem a $correu";
} else {
    echo "Si us plau, envia el formulari abans.";
}
?>