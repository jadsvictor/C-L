<?php

include 'daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$link = bd_connect();

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$dir = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
<<<<<<< HEAD
$file_daml = nome_arquivo_daml();
=======
$archive = nome_arquivo_daml();
>>>>>>> 17b18ca0149fe14af652f269d22b70dfc68dcf0e

$description = array("title" => "Ontologia de teste",
    "creator" => "Pedro",
    "description" => "teste de traducao de lexico para ontologia",
    "subject" => "",
    "versionInfo" => "1.1");

$list_concepts = get_lista_de_conceitos();
$list_relations = get_lista_de_relacoes();
<<<<<<< HEAD
$list_axioms = get_lista_de_axiomas();


$daml = salva_daml($site, $dir, $file_daml, $description, $list_concepts, $list_relations, $list_axioms);
=======
$list_axiom = get_lista_de_axiomas();


$daml = salva_daml($site, $dir, $archive, $i, $list_concepts, $list_relations, $list_axiom);
>>>>>>> 17b18ca0149fe14af652f269d22b70dfc68dcf0e

if (!$daml) {
    print 'Erro ao exportar ontologia para DAML!';
} else {
    print 'Ontologia exportada para DAML com sucesso! <br>';
    print 'Arquivo criado: ';
    print "<a href=\"$site$daml\">$daml</a>";
}


mysql_close($link);
?>