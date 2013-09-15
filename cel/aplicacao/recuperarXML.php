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
        <title>XML Recover</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php
//Cen�rio -  Gerar Relat�rios XML 
//Objetivo:   Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//             identificados por data.
//Contexto:   Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:     Administrador
//Recursos:   Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Restri��o: Recuperar os dados em XML do Banco de dados e os transformar
//                       por uma XSL para a exibi��o.

$database_recover = bd_connect() or die("Erro ao conectar ao SGBD");
if (isset($delete)) {
    if ($delete) {
        $qDelete = "DELETE FROM publicacao WHERE id_projeto = '$project_id' AND versao = '$version' ";
        $qrrDelete = mysql_query($qDelete);
    }
}
$selection = "SELECT * FROM publicacao WHERE id_projeto = '$project_id'";
$qrr = mysql_query($selection) or die("Erro ao enviar a query");
?>
    <h2>XML/XSL Recover</h2><br>
    <?php
    while ($result = mysql_fetch_row($qrr)) {
        $date = $result[1];
        $version = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Versao:</th>
                <td><?= $version ?></td>
                <th>Data:</th>
                <td><?= $date ?></td>
                <th>
                    <a href="mostraXML.php?id_projeto=<?= $project_id ?>&versao=<?= $version ?>">XML</a>
                </th>
                <th>
                    <a href="recuperarXML.php?id_projeto=<?= $project_id ?>&versao=<?= $version ?>&apaga=true">Apaga XML</a>
                </th>

            </tr>


        </table>

    <?php
}
?>

    <br><i><a href="showSource.php?file=recuperarXML.php">See the source!</a></i>

</body>

</html>
