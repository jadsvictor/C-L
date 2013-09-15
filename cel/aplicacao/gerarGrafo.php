<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");        // Checa se o usuario foi autenticado

$XML = "";
?>
<html>
    <body>
    <head>
        <title>Generate Graph</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php
//Cen�rio -  Gerar Grafo 
//Objetivo:   Permitir ao administrador gerar o grafo de um projeto
//Contexto:   Gerente deseja gerar um grafo para uma das vers�es de XML
//Atores:     Administrador
//Recursos:   Sistema, XML, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Restri��o: Possuir um XML gerado do projeto

$database_recover = bd_connect() or die("Erro ao conectar ao SGBD");
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

    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o c�digo fonte!</a></i>

</body>

</html>
