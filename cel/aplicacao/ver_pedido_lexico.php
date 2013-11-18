<html>
    <head>
        <title>Pedido Lexico</title>

        <?php
        
// Scenario - Check requests for alteration of terms of lexical
// Purpose : Allow the administrator to manage requests for alteration of terms in the lexicon .
// Context : Manager wish to view the applications for alteration of terms of the lexicon .
// Pre - Condition: Login , registered design .
// Actors : Administrator
// Resources : System database.
// Episodes : The administrator clicks the option of Check applications for alteration of terms of the lexicon .
// Restriction: Only the Project Manager may have this function visible .
// The system provides for a screen where the administrator will be able to view the historical
// All pending or not alterations to the terms of the lexicon .
// For new applications for inclusion or alteration of terms of the lexicon ,
// The system allows the administrator chooses Approve or Remove.
// For applications for inclusion or alteration already approved ,
// The system only enables the option to remove the administrator .
// To carry selections approval and removal , the administrator must click Process .

        session_start();

        include("funcoes_genericas.php");
        include("httprequest.inc");
        include("functionsBD/check_User_Authentication.php");

        checkUserAuthentication("index.php");

        if (isset($submit)) {

            $dataBase = new PGDB ();
            $update_request_lexicon = new QUERY($dataBase);
            for ($count = 0; $count < sizeof($request); $count++) {
                $update_request_lexicon->execute("update pedidolex set aprovado= 1 where id_pedido = $request[$count]");
                tratarPedidoLexico($request[$count]);
            }
            
            $delete_request_lexicon = new QUERY($dataBase);
            for ($count = 0; $count < sizeof($remove); $count++) {
                $delete_request_lexicon->execute("delete from pedidolex where id_pedido  = $remove[$count]");
                $delete_request_lexicon->execute("delete from sinonimo where id_pedidolex = $remove[$count]");
            }
            ?>

            <script type ="text/javascript1.3">
                opener.parent.frames['code'].location.reload();
                opener.parent.frames['text'].location.replace("main.php");
            </script>
        <h4>Operacao efetuada com sucesso!</h4>
        <script type ="text/javascript1.3">
            self.close();
        </script>
        <?php
    } else {
        ?>
    </head>
    <body>
        <h2>Pedidos de Alteracao no Lexico</h2>
        <form action="?id_projeto=<?= $project_id ?>" method="post">

            <?php
            $dataBase = new PGDB ();
            $select_request_lexicon = new QUERY($dataBase);
            
            $select_synonymous = new QUERY($dataBase);
            $select_request_lexicon->execute("SELECT * FROM pedidolex where id_projeto = $project_id");
            if ($select_request_lexicon->getntuples() == 0) {
                echo "<BR>Nenhum pedido.<BR>";
            } else {
                $record = $select_request_lexicon->gofirst();
                while ($record != 'LAST_RECORD_REACHED') {                    
                    $id_request = $record['id_pedido'];
                    $select_synonymous->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_request");
                    $select_user = new QUERY($dataBase);
                    $id_user = $record['id_usuario'];
                    $select_user->execute("SELECT * FROM usuario WHERE id_usuario = $id_user");
                    $user = $select_user->gofirst();
                    $requested_type = $record['tipo_pedido'];
                    if (strcasecmp($requested_type, 'remover')) {
                        ?>
                        <h3>O usuario 
                            <a  href="mailto:<?= $user['email'] ?>" >
                                <?= $user['nome'] ?>
                            </a> pede para <?= $requested_type ?> o lexico 
                            <font color="#ff0000">
                            <?= $record['nome'] ?>
                            </font>
                        </h3> <?
                            if (!strcasecmp($requested_type, 'alterar')) {
                                echo"para lexico abaixo:</h3>";
                            } else {
                                echo"</h3>";
                            }
                            ?>
                        <table>
                            <td><b>Nome:</b></td>
                            <td><?= $record['nome'] ?></td>

                            <tr>

                                <td><b>Nocao:</b></td>
                                <td><?= $record['nocao'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Impacto:</b></td>
                                <td><?= $record['impacto'] ?></td>
                            </tr>


                            <tr>
                                <td><b>Sinonimos:</b></td>
                                <td>
                                    <?php
                                    $synonymous = $select_synonymous->gofirst();
                                    $synonymous_string = "";
                                    while ($synonymous != 'LAST_RECORD_REACHED') {
                                        $synonymous_string = $synonymous_string . $synonymous["nome"] . ", ";
                                        $synonymous = $select_synonymous->gonext();
                                    }

                                    echo(substr($synonymous_string, 0, strrpos($synonymous_string, ",")));
                                    ?>
                                </td>
                            </tr>


                            <tr>
                                <td><b>Justificativa:</b></td>
                                <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                            </tr>
                        </table>
                    <?php } else {
                        ?>
                        <h3>O usuario 
                            <a  href="mailto:<?= $user['email'] ?>" >
                                <?= $user['nome'] ?>
                            </a> pede para <?= $requested_type ?> o lexico 
                            <font color="#ff0000">
                            <?= $record['nome'] ?>
                            </font>
                        </h3>
                        <?php
                    }
                    $okay = $record['aprovado'];
                    if ($okay == 1) {
                        echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
                    } else {
                        echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_request\"> <STRONG>Aprovar</STRONG>]<BR>  ";
                    }
                    echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_request\"> <STRONG>Remover da lista</STRONG>]";
                    print( "<br>\n<hr color=\"#000000\"><br>\n");
                    $record = $select_request_lexicon->gonext();
                }
            }
            ?>
            <input name="submit" type="submit" value="Processar">
        </form>
        <br><i><a href="showSource.php?file=ver_pedido_lexico.php">Veja o codigo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>

