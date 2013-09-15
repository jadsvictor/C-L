<?php
include("daml.php");
include("auxiliar_bd.php");
include_once("bd.inc");

session_start();


$link = bd_connect();

if ($_POST['user'] == "") {
// Recover user name
    $sql_user = "select nome from usuario where id_usuario='" . $_SESSION['id_usuario_corrente'] . "';";
    $query_user = mysql_query($sql_user) or die("Erro ao verificar usuario!" . mysql_error());
    $result = mysql_fetch_array($query_user);
    $user = $result[0];
} else {
    $user = $_POST['user'];
}

// Recover user name
$sql_project = "select nome from projeto where id_projeto='" . $_SESSION['id_projeto_corrente'] . "';";
$query_project = mysql_query($sql_project) or die("Erro ao verificar usu�rio!" . mysql_error());
$result = mysql_fetch_array($query_project);
$project = $result[0];

$site = $_SESSION['site'];
$dir = $_SESSION['diretorio'];
$archive = strtr($project, "������", "aaaooo") . "__" . date("j-m-Y_H-i-s") . ".daml";

$i = array("title" => $_POST['title'],
    "creator" => $user,
    "description" => $_POST['description'],
    "subject" => $_POST['subject'],
    "versionInfo" => $_POST['versionInfo']);

$_SESSION['id_projeto'] = $_SESSION['id_projeto_corrente'];
$list_concepts = get_lista_de_conceitos();
$list_relations = get_lista_de_relacoes();
$list_axiom = get_lista_de_axiomas();

$daml = salva_daml($site, $dir, $archive, $i, $list_concepts, $list_relations, $list_axiom);

mysql_close($link);
?>   

<html> 
    <head><title>Generate DAML</title></head> 
    <body style="background-color: #FFFFFF"> 

<?php
if (!$daml) {
    print 'Error exporting ontology for DAML!';
} else {

    print 'DAML ontology exported successfully! <br>';
    print 'Archive created: ';
    print "<a href=\"$site$daml\">$daml</a>";
}
?>  

    </body> 
</html> 