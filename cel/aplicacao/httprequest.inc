<?php

reset($_GET);

while (list ($chave, $valor) = each($_GET)) {
    $a = $chave;
    $b = $valor;
}

reset($_POST);

while (list ($chave, $valor) = each($_POST)) {
    $a = $chave;
    $b = $valor;
}
?>

