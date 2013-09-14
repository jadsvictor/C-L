<?php

include 'daml.php';
//include 'auxiliar_daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$link = bd_connect();

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$dir = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$archive = nome_arquivo_daml();

$i = array("title" => "Ontologia de teste",
    "creator" => "Pedro",
    "description" => "teste de tradu��o de l�xico para ontologia",
    "subject" => "",
    "versionInfo" => "1.1");

$list_concepts = get_lista_de_conceitos();
$list_relations = get_lista_de_relacoes();
$list_axiom = get_lista_de_axiomas();


$daml = salva_daml($site, $dir, $archive, $i, $list_concepts, $list_relations, $list_axiom);

if (!$daml) {
    print 'Erro ao exportar ontologia para DAML!';
} else {
    print 'Ontologia exportada para DAML com sucesso! <br>';
    print 'Arquivo criado: ';
    print "<a href=\"$site$daml\">$daml</a>";
}


mysql_close($link);
?>