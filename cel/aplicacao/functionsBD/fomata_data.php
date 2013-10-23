<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {

    function formataData($data) {

        $novaData = substr($data, 8, 9) .
                substr($data, 4, 4) .
                substr($data, 0, 4);
        return $novaData;
    }

}

?>
