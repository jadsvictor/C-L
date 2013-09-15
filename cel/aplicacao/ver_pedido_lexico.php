<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");

if (isset($submit)) {

    $dataBase = new PGDB ();
    $update_request_lexicon = new QUERY($dataBase);
    $delete_request_lexicon = new QUERY($dataBase);
    for ($count = 0; $count < sizeof($request); $count++) {
        $update_request_lexicon->execute("update pedidolex set aprovado= 1 where id_pedido = $request[$count]");
        tratarPedidoLexico($request[$count]);
    }
    for ($count = 0; $count < sizeof($remove); $count++) {
        $delete_request_lexicon->execute("delete from pedidolex where id_pedido  = $remove[$count]");
        $delete_request_lexicon->execute("delete from sinonimo where id_pedidolex = $remove[$count]");
    }
    ?>

    <script language="javascript1.2">
        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace("main.php");
    </script>
    <h4>Operacao efetuada com sucesso!</h4>
    <script language="javascript1.2">
        self.close();
    </script>
    <?php
} else {
    ?>
    <html>
        <head>
            <title>Pedido Lexico</title>
        </head>
        <body>
            <h2>Pedidos de Alteracao no Lexico</h2>
            <form action="?id_projeto=<?= $id_projeto ?>" method="post">

                <?php
// Cen�rio - Verificar pedidos de altera��o de termos do l�xico
//Objetivo:	Permitir ao administrador gerenciar os pedidos de altera��o de termos do l�xico.
//Contexto:	Gerente deseja visualizar os pedidos de altera��o de termos do l�xico.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:	Administrador
//Recursos:	Sistema, banco de dados.
//Epis�dios: 1- O administrador clica na op��o de Verificar pedidos de altera��o de termos do l�xico.
//           Restri��o: Somente o Administrador do projeto pode ter essa fun��o vis�vel.
//           2- O sistema fornece para o administrador uma tela onde poder� visualizar o hist�rico
//              de todas as altera��es pendentes ou n�o para os termos do l�xico.
//           3- Para novos pedidos de inclus�o ou altera��o de termos do l�xico,
//              O sistema permite que o administrador opte por Aprovar ou Remover.
//           4- Para os pedidos de inclus�o ou altera��o j� aprovados,
//              o sistema somente habilita a op��o remover para o administrador.
//           5- Para efetivar as sele��es de aprova��o e remo��o, o administrador deve clicar em Processar.

                $dataBase = new PGDB ();
                $select_request_lexicon = new QUERY($dataBase);
                $select_user = new QUERY($dataBase);
                $select_synonymous = new QUERY($dataBase);
                $select_request_lexicon->execute("SELECT * FROM pedidolex where id_projeto = $id_projeto");
                if ($select_request_lexicon->getntuples() == 0) {
                    echo "<BR>Nenhum pedido.<BR>";
                } else {
                    $record = $select_request_lexicon->gofirst();
                    while ($record != 'LAST_RECORD_REACHED') {
                        $id_user = $record['id_usuario'];
                        $id_request = $record['id_pedido'];
                        $requested_type = $record['tipo_pedido'];
                        $okay = $record['aprovado'];

                        $select_synonymous->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_request");

                        $select_user->execute("SELECT * FROM usuario WHERE id_usuario = $id_user");
                        $user = $select_user->gofirst();
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


