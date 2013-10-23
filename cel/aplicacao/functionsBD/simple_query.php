<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("simple_query"))) {

    function simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $q = "SELECT $field FROM $table WHERE $where";
        $qrr = mysql_query($q) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}
?>
