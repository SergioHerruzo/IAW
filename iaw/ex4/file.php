<?php
if ($_POST['enviar']) {
    $musica = $_POST['musica'];

    if ($musica == "Pop") {
        echo "T'agrada el Pop.";
    } elseif ($musica == "Rock") {
        echo "Ets de Rock.";
    } elseif ($musica == "Clàssica") {
        echo "Tens gusts refinats   .";
    } elseif ($musica == "Reggaeton") {
        echo "T’agrada el Reggaeton";
    } else {
        echo "No has escollit cap estil de música.";
    }
}
?>