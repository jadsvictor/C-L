<?php

include 'daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$connect_database = bd_connect();
if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXAO AO BD <br>";
        else
            echo "ERRO NA CONEXAO AO BD <br>";

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$dir = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
$arquivo = nome_arquivo_daml();

$description = array("title" => "Ontologia de teste",
    "creator" => "Pedro",
    "description" => "teste de tradução de léxico para ontologia",
    "subject" => "",
    "versionInfo" => "1.1");

$list_concept = get_lista_de_conceitos();
$list_relations = get_lista_de_relacoes();
$list_axioms = get_lista_de_axiomas();


$daml = salva_daml($site, $dir, $arquivo, $description, $list_concept, $list_relations, $list_axioms);

if (!$daml) {
    print 'Erro ao exportar ontologia para DAML!';
} else {
    print 'Ontologia exportada para DAML com sucesso! <br>';
    print 'Arquivo criado: ';
    print "<a href=\"$site$daml\">$daml</a>";
}


mysql_close($connect_database);
?>