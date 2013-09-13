<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");

$r = bd_connect() or die("Erro ao conectar ao SGBD");

$submit = 0;
if (isset($submit)) { 
    $deleta_usuario = "DELETE FROM participa
          WHERE id_usuario != " . $_SESSION['id_usuario_corrente'] . "
          AND id_projeto = " . $_SESSION['id_projeto_corrente'];
    mysql_query($deleta_usuario) or die("Erro ao executar a query de DELETE");

    $usuarios = 0;
    $numero_usuarios_selecionados = count($usuarios); 
    for ($i = 0; $i < $numero_usuarios_selecionados; $i++) {
        $cadastra_usuario = "INSERT INTO participa (id_usuario, id_projeto)
              VALUES (" . $usuarios[$i] . ", " . $_SESSION['id_projeto_corrente'] . ")";
        mysql_query($cadastra_usuario) or die("Erro ao cadastrar usuario");
    }
    ?>
     <script language="javascript1.3">

        self.close();

    </script>

    <?php
} else {
    ?>

    <html>
        <head>
            <title>
                Selecione os usuarios
            </title>
            <script language="javascript1.3" src="MSelect.js">
            </script>
            <script language="javascript1.3">

                function createMSelect()
                {
                    var usr_lselect = document.forms[0].elements['usuarios[]'];
                    var usr_rselect = document.forms[0].usuarios_r;
                    var usr_l2r = document.forms[0].usr_l2r;
                    var usr_r2l = document.forms[0].usr_r2l;
                    var MS_usr = new MSelect(usr_lselect, usr_rselect, usr_l2r, usr_r2l);
                }

                function selAll()
                {
                    var usuarios = document.forms[0].elements['usuarios[]'];
                    for (var i = 0; i < usuarios.length; i++)
                        usuarios.options[i].selected = true;
                }

            </script>
            <style>
               select {
                    width: 200px;
                    background-color: #CCFFFF
                }
             </style>
        </head>
        <body onLoad="createMSelect();">
            <h4>Selecione os usuarios para participar do projeto 
                "<span style="color: orange">
    <?= simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']) ?>
                </span>":
            </h4>
            <p style="color: red">
                Mantenha <strong>CTRL</strong> pressionado para selecionar multiplas opcoes
            </p>
            <form action="" method="post" onSubmit="selAll();">
               table{
                    border-spacing:10px;
                    width="100%";
                }
                td{
                    align="center";
                    style="color: green";
                }
                tr{
                     align="center";
                }
                
                <table>
                    <tr>
                        <td>Participantes:</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            <select name="usuarios[]" multiple size="6">
    <?php
    $seleciona_usuario = "SELECT u.id_usuario, login
                                              FROM usuario u, participa p
                                              WHERE u.id_usuario = p.id_usuario
                                              AND p.id_projeto = " . $_SESSION['id_projeto_corrente'] . "
                                              AND u.id_usuario != " . $_SESSION['id_usuario_corrente'];

    $qrr = mysql_query($seleciona_usuario) or die("Erro ao enviar a query");
    while ($result_usuario_selecionado = mysql_fetch_array($qrr)) {
        ?>
                                    <option value="<?= $result_usuario_selecionado['id_usuario'] ?>">
                                    <?= $result_usuario_selecionado['login'] ?>
                                    </option>

                                      
                               <?php
                                }
                                ?>

                            </select>
                        </td>
                        <td>
                            <input name="usr_l2r" type="button" value="->">
                        </td>
                        <td rowspan="2">
                            <select  multiple name="usuarios_r" size="6">
    <?php
    $seleciona_usuario_nao_participante = "SELECT id_usuario FROM participa where participa.id_projeto =" . $_SESSION['id_projeto_corrente'];
    $subqrr = mysql_query($seleciona_usuario_nao_participante) or die("Erro ao enviar a subquery");
    $resultado_usuario_nao_participante = "(0)";
    if ($subqrr != 0) {
        $row = mysql_fetch_row($subqrr);
        $resultado_usuario_nao_participante = "( $row[0]";
        while ($row = mysql_fetch_row($subqrr))
            $resultado_usuario_nao_participante = "$resultado_usuario_nao_participante , $row[0]";
        $resultado_usuario_nao_participante = "$resultado_usuario_nao_participante )";
    }
    $q = "SELECT usuario.id_usuario, usuario.login FROM usuario where usuario.id_usuario not in " . $resultado_usuario_nao_participante;
   
    echo($q);
    $query_usuario_nao_participante = mysql_query($q) or die("Erro ao enviar a query");
    while ($result = mysql_fetch_array($query_usuario_nao_participante)) {
        ?>
                                    <option value="<?= $result['id_usuario'] ?>">
                                    <?= $result['login'] ?>
                                    </option>

                               <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>
                            <input name="usr_r2l" type="button" value="<-">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="3" height="50" valign="bottom">
                            <input name="submit" type="submit" value="Atualizar">
                        </td>
                    </tr>
                </table>
              </form>
            <br><i><a href="showSource.php?file=rel_usuario.php">Veja o codigo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
