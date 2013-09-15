<?php

include 'bd.inc';

$link = bd_connect();


$query_lexicon = "update lexico set tipo =  NULL;";
mysql_query($query_lexicon) or die("A consulta ao BD falhou : " . mysql_error());

mysql_close($link);
?>