<?php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

// Check if the User is authenticated
checkUserAuthentication("index.php");        

bd_connect() or die("Erro ao conectar ao SGBD");

// Scenario - Generate XML Reports
// Purpose: Allow the administrator to generate reports in XML format to a project,
// Identified by date.
// Context: Manager to generate a report for a project which is administrator.
// Precondition: Login, registered design.
// Actors: Administrator
// Resources: System, report data, data registered design, database.
// Episodes: Generating Success with the report from the data registered design,
// The system gives the administrator the viewing screen of the report
// XML created.

$qq = "select * from publicacao where id_projeto = $project_id AND versao = $version";
$qrr = mysql_query($qq) or die("Erro ao enviar a query");
$row = mysql_fetch_row($qrr);
$xml_bank = $row[3];

echo $xml_bank;
?>
