<?php

include("funcoes_genericas.php");
include("httprequest.inc");

//Cen�rio  -  Escolher Projeto
//Objetivo:     Permitir ao Administrador/Usu�rio escolher um projeto.
//Contexto:     O Administrador/Usu�rio deseja escolher um projeto.
//              Pr�-Condi��es: Login, Ser Administrador
//Atores:       Administrador, Usu�rio
//Recursos:     Usu�rios cadastrados
//Epis�dios:    Caso o Usuario selecione da lista de projetos um projeto da qual ele seja administrador,
//              ver ADMINISTRADOR ESCOLHE PROJETO.
//              Caso contr�rio, ver USU�RIO ESCOLHE PROJETO.

$database_recover = bd_connect() or die("Erro ao conectar ao SGBD");

$qq = "select * from publicacao where id_projeto = $project_id AND versao = $version";
$qrr = mysql_query($qq) or die("Erro ao enviar a query");
$row = mysql_fetch_row($qrr);
$xml_bank = $row[3];

echo $xml_bank;
?>
