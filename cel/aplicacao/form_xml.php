<?php
session_start();

include("funcoes_genericas.php");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");        // Checa se o usuario foi autenticado
?>

<html>

    <body>

    <head>
        <title>Gerar XML</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head><form action="gerador_xml.php" method="post">

        <h2>Propriedades do Relat�rio a ser Gerado:</h2>
        <?php
        $today = getdate();
        ?>

        &nbsp;Data da Vers�o:
        <?= $today['mday']; ?>/<?= $today['mon']; ?>/<?= $today['year']; ?>
        <p>&nbsp;<input type="hidden" name="data_dia" size="3" value="<?= $today['mday']; ?>">
            <input  type="hidden" name="data_mes" size="3" value="<?= $today['mon']; ?>">
            <input type="hidden" name="data_ano" size="6" value="<?= $today['year']; ?>">

            &nbsp;</p>
        Vers�o do XML: &nbsp;<input type="text" name="versao" size="15">
        <p>Exibir

            Formatado: <input type="checkbox" name="flag" value="ON"><br><br>

            <input type="submit" value="Gerar"> </p>

    </form>
    <br><i><a href="showSource.php?file=form_xml.php">Veja o c�digo fonte!</a></i>
</body>

</html>
