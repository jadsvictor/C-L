<?php
session_start();

include("functionsBD/simple_query.php");
include("funcoes_genericas.php");
include ("functionsPage/recarrega.php");
include ("functionsProject/check_proj_perm.php");
include("functionsBD/is_admin.php");

chkUser("index.php");
?>

<html>
    <head>
        <script language="javascript1.3">

            // Funcoes que serao usadas quando o script
            // for chamado atraves dele proprio ou da arvore
            function reCarrega(URL) {
                document.location.replace(URL);
            }

            function altCenario(cenario) {
                var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function rmvCenario(cenario) {
                var url = 'rmv_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function altLexico(lexico) {
                var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function rmvLexico(lexico) {
                var url = 'rmv_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            // Funcoes que serao usadas quando o script
            // for chamado atraves da heading.php
            function pedidoCenario() {
                var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $project_id ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function pedidoLexico() {
                var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $project_id ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function addUsuario() {
                var url = 'add_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=270,width=490,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function relUsuario() {
                var url = 'rel_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function geraXML() {
                var url = 'xml_gerador.php?id_projeto=' + '<?= $project_id ?>';
                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }
        </script>
        <script type="text/javascript" src="mtmtrack.js">
        </script>
    </head>
    <body>

        <?php
        include("frame_inferior.php");

        if (isset($id) && isset($t)) {
            if ($t == "c") {
                ?>

                <h3>Informa��es sobre o cen�rio</h3>

                <?php
            }
             else {
                ?>

                <h3>Informa��es sobre o l�xico</h3>

                <?php
            }
       
            ?>

            <table>

                <?php
                $c = bd_connect() or die("Erro ao conectar ao SGBD");

                if ($t == "c") {
                    $selection = "SELECT id_cenario, titulo, objetivo, contexto, 
                                  atores, recursos, episodios
              FROM cenario
              WHERE id_cenario = $id";
                    $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");
                    $result = mysql_fetch_array($qrr);
                    ?>

                    <tr>
                        <td>Titulo:</td><td><?= $result['titulo'] ?></td>
                    </tr>
                    <tr>
                        <td>Objetivo:</td><td><?= $result['objetivo'] ?></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td><td><?= $result['contexto'] ?></td>
                    </tr>
                    <tr>
                        <td>Atores:</td><td><?= $result['atores'] ?></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td><td><?= $result['recursos'] ?></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td><td><?= $result['episodios'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="altCenario(<?= $result['id_cenario'] ?>);">Alterar Cen�rio</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="rmvCenario(<?= $result['id_cenario'] ?>);">Remover Cen�rio</a>
                        </td>
                    </tr>

                    <?php
                } 
                
                else {
                    $selection = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_lexico = $id";
                    $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");
                    $result = mysql_fetch_array($qrr);
                    ?>

                    <tr>
                        <td>Nome:</td><td><?= $result['nome'] ?></td>
                    </tr>
                    <tr>
                        <td>No��o:</td><td><?= $result['nocao'] ?></td>
                    </tr>
                    <tr>
                        <td>Impacto:</td><td><?= $result['impacto'] ?></td>
                    </tr>
                    <tr>
                        <td height="40" valign="bottom">
                            <a href="#" onClick="altLexico(<?= $result['id_lexico'] ?>);">Alterar L�xico</a>
                        </td>
                        <td valign="bottom">
                            <a href="#" onClick="rmvLexico(<?= $result['id_lexico'] ?>);">Remover L�xico</a>
                        </td>
                    </tr>

                    <?php
                }
                ?>

            </table>
            <br>
            <br>
            <br>

            <?php
            if ($t == "c") {
                ?>

                <h3>Cen�rios que referenciam este cen�rio</h3>

                <?php
            }
            
            else {
                ?>

                <h3>Cen�rios e termos do l�xico que referenciam este termo</h3>

                <?php
            }

            frame_inferior($c, $t, $id);
        } 
        
        elseif (isset($project_id)) {
             // Was passed a variable $ id_projeto. This variable should contain the id of a
������������ // Project that the User is registered. However, as the passage eh
������������ // Done using JavaScript (in heading.php), we check if this id really
������������ // Corresponds to a project that the User has access (security).
            check_proj_perm($_SESSION['id_usuario_corrente'], $project_id) or die("Permissao negada");

            $_SESSION['id_projeto_corrente'] = $project_id;
            ?>

            <table>
                <tr>
                    <td>Projeto:</td>
                    <td><?= simple_query("nome", "projeto", "id_projeto = $project_id") ?></td>
                </tr>
                <tr>
                    <td>Data de cria��o:</td>
                    <td><?= simple_query("TO_CHAR(data_criacao, 'DD/MM/YY')", "projeto", "id_projeto = $project_id") ?></td>
                </tr>
                <tr>
                    <td>Descri��o:</td>
                    <td><?= simple_query("descricao", "projeto", "id_projeto = $project_id") ?></td>
                </tr>
            </table>

            <?php
            if (is_admin($_SESSION['id_usuario_corrente'], $project_id)) {
                ?>

                <br>
                <p><b>Voc� � um administrador deste projeto</b></p>
                <p><a href="#" onClick="pedidoCenario();">Verificar pedidos de altera��o de Cen�rios</a></p>
                <p><a href="#" onClick="pedidoLexico();">Verificar pedidos de altera��o de termos do L�xico</a></p>
                <p><a href="#" onClick="addUsuario();">Adicionar usu�rio (n�o existente) neste projeto</a></p>
                <p><a href="#" onClick="relUsuario();">Relacionar usu�rios j� existentes com este projeto</a></p>
                <p><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></p>

                <?php
            }
            else {
            	//nothing to do
            }
        } 
        
        else {        // SCRIPT CHAMADO PELO INDEX.PHP
            ?>

            <p>Selecione um projeto acima, ou crie um novo projeto.</p>

            <?php
        }
        ?>

    </body>
</html>

