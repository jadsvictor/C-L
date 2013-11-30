<?php

include 'bd.inc';

$connect_database = bd_connect();
if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
    echo "SUCESSO NA CONEXAO AO BD <br>";
else
    echo "ERRO NA CONEXAO AO BD <br>";


$query_lexicon = "update lexico set tipo =  NULL;";
mysql_query($query_lexicon) or die("A consulta ao BD falhou : " . mysql_error());

mysql_close($connect_database);
?>