<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");

$XML = "";
?>
<html>

    <head>
        <title>XML Recover</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>
    <body>

        <?php
// Scenario - Generate XML Reports
// Purpose: Allow the administrator to generate reports in XML format to a project,
// Identified by date.
// Context: Manager to generate a report for a project which is administrator.
// Precondition: Login, registered design.
// Actors: Administrator
// Resources: System, report data, data registered design, database.
// Episodes: Restriction: Retrieve XML data from the database and transform
// By an XSL for display.

        bd_connect() or die("Erro ao conectar ao SGBD");
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
