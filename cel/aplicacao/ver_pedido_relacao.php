<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");
if (isset($submit)) {
    $dataBase = new PGDB ();
    $select_request_relation = new QUERY($dataBase);
    $update_request_relation = new QUERY($dataBase);
    $delete_request_relation = new QUERY($dataBase);
    for ($count = 0; $count < sizeof($request); $count++) {
        $update_request_relation->execute("update pedidorel set aprovado= 1 where id_pedido = $request[$count]");
        tratarPedidoRelacao($request[$count]);
    }
    for ($count = 0; $count < sizeof($remove); $count++) {
        $delete_request_relation->execute("delete from pedidorel where id_pedido = $remove[$count]");
    }
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Operacao efetuada com sucesso!</h4>
    <script language="javascript1.3">

        self.close();

    </script>

<?php } else {
    ?>
    <html>
        <head>
            <title>Pedidos de alteracao das Relacoes</title>
        </head>
        <body>
            <h2>Pedidos de Alteracao no Conjunto de Relacoes</h2>
            <form action="?id_projeto=<?= $id_projeto ?>" method="post">

                <?php
// Cen�rio - Verificar pedidos de altera��o de conceitos
//Objetivo:	Permitir ao administrador gerenciar os pedidos de altera��o de conceitos.
//Contexto:	Gerente deseja visualizar os pedidos de altera��o de conceitos.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:	Administrador
//Recursos:	Sistema, banco de dados.
//Epis�dios: O administrador clica na op��o de Verificar pedidos de altera��o de cen�rios.
//           Restri��o: Somente o Administrador do projeto pode ter essa fun��o vis�vel.
//           O sistema fornece para o administrador uma tela onde poder� visualizar o hist�rico
//           de todas as altera��es pendentes ou n�o para os cen�rios.
//           Para novos pedidos de inclus�o ou altera��o de cen�rios,
//           o sistema permite que o administrador opte por Aprovar ou Remover.
//           Para os pedidos de inclus�o ou altera��o j� aprovados,
//           o sistema somente habilita a op��o remover para o administrador.
//           Para efetivar as sele��es de aprova��o e remo��o, basta clicar em Processar.

                $dataBase = new PGDB ();
                $select_request_relation = new QUERY($dataBase);
                $select_user = new QUERY($dataBase);
                $select_request_relation->execute("SELECT * FROM pedidorel WHERE id_projeto = $id_projeto");
                if ($select_request_relation->getntuples() == 0) {
                    echo "<BR>Nenhum pedido.<BR>";
                } else {
                    $record = $select_request_relation->gofirst();

                    while ($record != 'LAST_RECORD_REACHED') {
                        $id_user = $record['id_usuario'];
                        $id_request = $record['id_pedido'];
                        $requested_type = $record['tipo_pedido'];
                        $okay = $record['aprovado'];
                        $select_user->execute("SELECT * FROM usuario WHERE id_usuario = $id_user");
                        $user = $select_user->gofirst();
                        if (strcasecmp($requested_type, 'remover')) {
                            ?>

                            <br>
                            <h3>O usuario 
                                <a  href="mailto:<?= $user['email'] ?>" >
                                    <?= $user['nome'] ?>
                                </a> pede para <?= $requested_type ?> a relacao 
                                <font color="#ff0000">
                                <?= $record['nome'] ?>
                                </font> 
                            </h3> <?
                            if (!strcasecmp($requested_type, 'alterar')) {
                                echo"para conceito abaixo:</h3>";
                            } else {
                                echo"</h3>";
                            }
                            ?>
                            <table>
                                <td><b>Nome:</b></td>
                                <td><?= $record['nome'] ?></td>
                                <tr>
                                    <td><b>Justificativa:</b></td>
                                    <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                                </tr>
                            </table>
                        <?php } else { ?>
                            <h3>O usuario 
                                <a  href="mailto:<?= $user['email'] ?>" >
                                    <?= $user['nome'] ?>
                                </a> pede para <?= $requested_type ?> a relacao 
                                <font color="#ff0000">
                                    <?= $record['nome'] ?>
                                </font>
                            </h3>
                            <?php
                        }
                        if ($okay == 1) {
                            echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
                        } else {
                            echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_request\"> <STRONG>Aprovar</STRONG>]<BR>  ";
                        }
                        echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_request\"> <STRONG>Remover da lista</STRONG>]";
                        print( "<br>\n<hr color=\"#000000\"><br>\n");
                        $record = $select_request_relation->gonext();
                    }
                }
                ?>
                <input name="submit" type="submit" value="Processar">
            </form>
            <br><i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o codigo fonte!</a></i>
        </body>
    </html>
    <?php
}
?>

