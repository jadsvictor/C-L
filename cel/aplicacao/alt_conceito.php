<?php

// Alt_conceito.php: This script makes a request for alteration of a design concept.
// The User receives a form with the current concept (ie with completed fields)
// And may make changes in all fields nome.Ao least in the end the main screen
// Returns to the start screen and the tree and fechada.O form of alteration and tb closed.
// File Caller: main.php.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

chkUser("index.php");

$database_conection = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    inserirPedidoAlterarConceito($_SESSION['id_projeto_corrente'], $id_conceito, $nome, $descricao, $namespace, $justificativa, $_SESSION['id_usuario_corrente']);
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
} else {
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $selection = "SELECT * FROM conceito WHERE id_conceito = $id_conceito";
    $qrr = mysql_query($selection) or die("Erro ao executar a query");
    $result = mysql_fetch_array($qrr);

// Scenario - Changing Concept

// Purpose: Allow changing a concept for a user
// Context: User want to change concept previously registered
// Precondition: Login, Scenario registered in the system
// Actors: User
// Resources: System, data registered
// Episodes: The system will provide to the user the same screen INCLUDE SCENARIO,
// But with the following scenario data to be changed filled
// And editable in their respective fields: Purpose, Context, Actors, Resources and Episodes.
// Fields Project Title and will be filled, but not editable.
// Will display a field Rationale for the user to place a
// Justification for the change made.
    ?>

    <html>
        <head>
            <title>Alterar Conceito</title>
        </head>
        <body>
            <h4>Alterar Conceito</h4>
            <br>
            <form action="?id_projeto=<?= $project_id ?>" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <input type="hidden" name="id_conceitos" value="<?= $result['id_conceito'] ?>">
                    <td>Nome:</td>
    <? $result['nome'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['nome']); ?>
                    <input type="hidden" name="nome" value="<?= $result['nome'] ?>">
                    <td><input disabled maxlength="128" name="nome2" size="48" 
                               type="text" value="<?= $result['nome'] ?>"></td>
                    <tr>
                        <td>Descricao:</td>
    <? $result['descricao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['descricao']);
    ?>

                        <td><textarea name="descricao" cols="48" rows="3"><?= $result['descricao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Namespace:</td>
    <? $result['namespace'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['namespace']);
    ?>
                        <td><textarea name="namespace" cols="48" rows="3"><?= $result['namespace'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                        <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input 
                                name="submit" type="submit" value="Alterar Cen�rio" 
                                onClick="updateOpener();"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=alt_cenario.php">Veja o c�digo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
