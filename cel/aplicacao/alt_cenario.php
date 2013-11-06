<?php

// Alt_cenario.php: This script makes a request for alteration of a scenario project.
// The User receives a form with the current scenario (ie with completed fields)
// And may make changes in all fields titulo.Ao least in the end the main screen
// Returns to the start screen and the tree and fechada.O form of alteration and tb closed.
// File Caller: main.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");
include("functionsBD/simple_query.php");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");

$database_conection = bd_connect() or die("Erro ao conectar ao SGBD");
if (isset($submit)) {
    inserirPedidoAlterarCenario($_SESSION['id_projeto_corrente'], $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $justificativa, $_SESSION['id_usuario_corrente']);
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Opera��o efetuada com sucesso!</h4>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
} 

else {
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $selection = "SELECT * FROM cenario WHERE id_cenario = $id_cenario";
    $qrr = mysql_query($selection) or die("Erro ao executar a query");
    $result = mysql_fetch_array($qrr);

// Scenario - Changing Scenario
// Purpose: Allow changing a setting by a user
// Context: User want to change scenario previously registered
// Precondition: Login, Scenario registered in the system
// Actors: User
// Resources: System, data registered
// Exceptions: The scenario name being changed is changed to the name of an existing scenario.
// Episodes: The system will provide to the user the same screen INCLUDE SCENARIO,
// But with the following scenario data to be changed filled
// And editable in their respective fields: Purpose, Context, Actors, Resources and Episodes.
// Fields Project Title and will be filled, but not editable.
// Will display a field Rationale for the user to place a
// Justification for the change made.
    ?>

    <html>
        <head>
            <title>Alterar Cen�rio</title>
        </head>
        <body>
            <h4>Alterar Cen�rio</h4>
            <br>
            <form action="?id_projeto=<?= $project_id ?>" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <input type="hidden" name="id_cenario" value="<?= $result['id_cenario'] ?>">
                    <td>T�tulo:</td>
    <? $result['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['titulo']); ?>
                    <input type="hidden" name="titulo" value="<?= $result['titulo'] ?>">
                    <td><input disabled maxlength="128" name="titulo2" size="48" 
                               type="text" value="<?= $result['titulo'] ?>"></td>
                    <tr>
                        <td>Objetivo:</td>
    <? $result['objetivo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['objetivo']); ?>

                        <td><textarea name="objetivo" cols="48" rows="3"><?= $result['objetivo'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
    <? $result['contexto'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['contexto']); ?>
                        <td><textarea name="contexto" cols="48" rows="3"><?= $result['contexto'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
    <? $result['atores'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['atores']); ?>

                        <td><textarea name="atores" cols="48" rows="3"><?= $result['atores'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
    <? $result['recursos'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['recursos']); ?>

                        <td><textarea name="recursos" cols="48" rows="3"><?= $result['recursos'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce��o:</td>
    <? $result['excecao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['excecao']); ?>

                        <td><textarea name="excecao" cols="48" rows="3"><?= $result['excecao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td>
    <? $result['episodios'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['episodios']); ?>
                        <td><textarea  cols="48" name="episodios" rows="5"><?= $result['episodios'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                        <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
                    </tr>

                    <tr>
                        <td colspan="2"><b><small>Essa justificativa � necess�ria 
                                    apenas para aqueles usu�rios que 
                                    n�o s�o administradores.</small></b></td>
                    </tr>

                    <tr>
                        <td align="center" colspan="2" height="60"><input 
                                name="submit" type="submit" value="Alterar Cen�rio" 
                                onClick="updateOpener()"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=alt_cenario.php">Veja o c�digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
