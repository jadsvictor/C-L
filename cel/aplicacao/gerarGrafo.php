<html>
    <head>
        <title>Generate Graph</title>
<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

// Check if the user was authenticated
checkUserAuthentication("index.php");        

$XML = "";
?>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

    <body>
<?php
//Scenario -  Generate Graph
//Goal:   Allow the administrator to generate the graph of a project
//Context:   Manager to generate a graph for one of the versions of XML
//Actors:     Administrator
//Means:   System, XML, registered data project, database.
//Episodes:  Restriction: Possuir um XML gerado do projeto

bd_connect() or die("Erro ao conectar ao SGBD");
$selection = "SELECT * FROM publicacao WHERE id_projeto = '$project_id'";
$qrr = mysql_query($selection) or die("Erro ao enviar a query");
?>
    <h2>Generate Graph</h2><br>
    <?php
    while ($result = mysql_fetch_row($qrr)) {
        $date = $result[1];
        $version = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Version:</th><td><?= $version ?></td>
                <th>Date:</th><td><?= $date ?></td>
                <th><a href="mostraXML.php?id_projeto=<?= $project_id ?>&versao=<?= $version ?>">XML</a></th>
                <th><a href="grafo\mostraGrafo.php?versao=<?= $version ?>&id_projeto=<?= $project_id ?>">Gerar Grafo</a></th>

            </tr>
        </table>

    <?php
}
?>

    <br><i><a href="showSource.php?file=recuperarXML.php">See the source!</a></i>

</body>

</html>
