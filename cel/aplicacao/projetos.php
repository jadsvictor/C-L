<?php
include("funcoes_genericas.php");
?>
<html>

    <head>
    <p style="color: red; font-weight: bold; text-align: center">
        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
        Projetos Publicados</p>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<?php
$database_recover = bd_connect() or die("Erro ao conectar ao SGBD");

//Cen�rio - Escolher Projeto
//Objetivo:   Permitir ao Administrador/Usu�rio escolher um projeto.
//Contexto:   O Administrador/Usu�rio deseja escolher um projeto.
//            Pr�-Condi��es: Login, Ser Administrador
//Atores:     Administrador, Usu�rio
//Recursos:   Usu�rios cadastrados
//Epis�dios:  Caso o Usuario selecione da lista de projetos um projeto da qual ele seja administrador,
//            ver ADMINISTRADOR ESCOLHE PROJETO.
//            Caso contr�rio, ver USU�RIO ESCOLHE PROJETO.

$selection = "SELECT * FROM publicacao";
$qrr = mysql_query($selection) or die("Erro ao enviar a query de busca");
?>

    <?php
    while ($result = mysql_fetch_row($qrr)) {
        $project_id = $result[0];
        $date = $result[1];
        $version = $result[2];
        $XML = $result[3];

        $qSearchProjectName = "SELECT * FROM projeto WHERE id_projeto = '$project_id'";
        $qrrSearch = mysql_query($qSearchProjectName) or die("Erro ao enviar a query de busca de projeto");
        $resultName = mysql_fetch_row($qrrSearch);
        $project_name = $resultName[1];
        ?>
        <table border='0'>

            <tr>

                <th height="29" width="140"><a href="mostrarProjeto.php?id_projeto=<?= $project_id ?>&versao=<?= $version ?>"><?= $project_name ?></a></th>
                <th height="29" width="140">Data: <?= $date ?></th>
                <th height="29" width="100">Vers�o: <?= $version ?></th>

            </tr>


        </table>

    <?php
}
?>


</body>

</html>