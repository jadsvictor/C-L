<html>
    <head>
        <title>
            Pedidos de alteração dos Cenários
        </title>

        <?php
// Setting - Check order change scenarios
// Purpose : Allow the administrator to manage requests for change scenarios .
// Context : Manager wish to view the applications change scenarios .
// Precondition : Login , registered design .
// Actors : Administrator
// Resources : System database.
// Episodes : The administrator clicks the option Check applications change scenarios .
// Restriction: Only the Project Manager may have this function visible.
// The system provides the administrator a screen where you can view the history
// All pending changes or not for the scenarios .
// For new applications included the � or change scenarios ,
// The system allows the administrator chooses Approve or Remove.
// For requests to add or change already approved ,
// The system only enables the option to remove the administrator .
// To commit the selections approval and removal , simply click Process .
        session_start();

        include("funcoes_genericas.php");
        include("httprequest.inc");
        include("functionsBD/check_User_Authentication.php");

        checkUserAuthentication("index.php");
        if (isset($submit)) {
            $dataBase = new PGDB ();
            $update_request_scene = new QUERY($dataBase);
            for ($count = 0; $count < sizeof($request); $count++) {
                $update_request_scene->execute("update pedidocen set aprovado= 1 where id_pedido = $request[$count]");
                tratarPedidoCenario($request[$count]);
            }

            $delete_request_scene = new QUERY($dataBase);
            for ($count = 0; $count < sizeof($remove); $count++) {
                $delete_request_scene->execute("delete from pedidocen where id_pedido = $remove[$count]");
            }
            ?>

            <script type ="text/javascript1.3">
                opener.parent.frames['code'].location.reload();
                opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>');

            </script>

        <h4>Operação efetuada com sucesso!</h4>
        <script type ="text/javascript1.3">

            self.close();

        </script>

    <?php } else {
        ?>

    </head>

    <body>
        <h2>Pedidos de Alteração no Conjunto de Cenários</h2>
        <form action="?id_projeto=<?= $id_project ?>" method="post">

            <?php
            $dataBase = new PGDB ();
            $select_request_scene = new QUERY($dataBase);
            $select_request_scene->execute("SELECT * FROM pedidocen WHERE id_projeto = $id_project");

            if ($select_request_scene->getntuples() == 0) {
                echo "<BR>Nenhum pedido.<BR>";
            } else {
                $record = $select_request_scene->gofirst();
                while ($record != 'LAST_RECORD_REACHED') {
                    $select_user = new QUERY($dataBase);
                    $id_user = $record['id_usuario'];
                    $select_user->execute("SELECT * FROM usuario WHERE id_usuario = $id_user");
                    $user = $select_user->gofirst();
                    $requested_type = $record['tipo_pedido'];
                    if (strcasecmp($requested_type, 'remover')) {
                        ?>

                        <br>
                        <h3>O usuario 
                            <a  href="mailto:<?= $user['email'] ?>" >
                                <?= $user['nome'] ?>
                            </a> pede para <?= $requested_type ?> o cenário 
                            <font color="#ff0000">
                            <?= $record['titulo'] ?>
                            </font>
                        </h3> <?
                        if (!strcasecmp($requested_type, 'alterar')) {
                            echo"para cenário abaixo:</h3>";
                        } else {
                            echo"</h3>";
                        }
                        ?>
                        <table>
                            <td><b>Título:</b></td>
                            <td><?= $record['titulo'] ?></td>
                            <tr>
                                <td><b>Objetivo:</b></td>
                                <td><?= $record['objetivo'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Contexto:</b></td>
                                <td><?= $record['contexto'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Atores:</b></td>
                                <td><?= $record['atores'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Recursos:</b></td>
                                <td><?= $record['recursos'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Exceção:</b></td>
                                <td><?= $record['excecao'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Episódios:</b></td>
                                <td><textarea cols="48" name="episodios" rows="5"><?= $record['episodios'] ?></textarea></td>
                            </tr>
                            <tr>
                                <td><b>Justificativa:</b></td>
                                <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                            </tr>
                        </table>
                    <?php } else { ?>
                        <h3>O usuário 
                            <a  href="mailto:<?= $user['email'] ?>" >
                                <?= $user['nome'] ?>
                            </a> pede para <?= $requested_type ?> o cenário 
                            <font color="#ff0000">
                            <?= $record['titulo'] ?>
                            </font></h3>
                        <?php
                    }
                    $okay = $record['aprovado'];
                    $id_request = $record['id_pedido'];
                    if ($okay == 1) {
                        echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
                    } else {
                        echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_request\"> <STRONG>Aprovar</STRONG>]<BR>  ";
                    }
                    echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_request\"> <STRONG>Remover da lista</STRONG>]";
                    print( "<br>\n<hr color=\"#000000\"><br>\n");
                    $record = $select_request_scene->gonext();
                }
            }
            ?>
            <input name="submit" type="submit" value="Processar">
        </form>
        <br><i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o código fonte!</a></i>
    </body>
    </html>
    <?php
}
?>