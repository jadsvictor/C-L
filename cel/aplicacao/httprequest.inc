<?php

reset($_GET);
while (list ($key, $value) = each($_GET)) {
    $a = $key;
    $b = $value;
}

reset($_POST);
while (list ($key, $value) = each($_POST)) {
    $a = $key;
    $b = $value;
}
?>
