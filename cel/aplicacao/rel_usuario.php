 <html>
        <head>
            <title>
                Selecione os usuarios
            </title>

<?php
// Scenario - Users' Relate to the project
// Purpose: Allow the Administrator to relate new users registered to the selected project.
// Context: The Administrator want to relate new registered users to the selected project.
// Preconditions: Being the project manager who want to relate the users
// Actors: Administrator
// Features: Registered Users

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("bd.inc");
include("functionsBD/simple_query.php");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");

$connect_database = bd_connect() or die("Erro ao conectar ao SGBD");
if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXAO AO BD <br>";
        else
            echo "ERRO NA CONEXAO AO BD <br>";

$submit = 0;
if (isset($submit)) {
    $delete_user = "DELETE FROM participa
          WHERE id_usuario != " . (int)$_GET['id_usuario_corrente'] . "
          AND id_projeto = " . (int)$_GET['id_projeto_corrente'];
    mysql_query($delete_user) or die("Erro ao executar a query de DELETE");
    
    $user = 0;
    $number_selected_users = count($user);
    for ($i = 0; $i < $number_selected_users; $i++) {
        $user_registers = "INSERT INTO participa (id_usuario, id_projeto)
              VALUES (" . $user[$i] . ", " . (int)$_GET['id_projeto_corrente'] . ")";
        mysql_query($user_registers) or die("Erro ao cadastrar usuario");
    }
    ?>
    <script type ="text/javascript1.3">

        self.close();

    </script>

    <?php
} else {
    ?>

   
            <script type ="text/javascript1.3" src="MSelect.js">
            </script>
            <script>
                    
                function createMSelect() {
                    var usr_lselect = document.forms[0].elements['usuarios[]'];
                    var usr_rselect = document.forms[0].usuarios_r;
                    var usr_l2r = document.forms[0].usr_l2r;
                    var usr_r2l = document.forms[0].usr_r2l;
                    var MS_usr = new MSelect(usr_lselect, usr_rselect, usr_l2r, usr_r2l);
                }

                function selAll(){
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
                                // Episodio 1: O Administrador clica no link Relacionar usuario ja existentes com este projeto.
                                $user_selects = "SELECT u.id_usuario, login
                                              FROM usuario u, participa p
                                              WHERE u.id_usuario = p.id_usuario
                                              AND p.id_projeto = " . (int)$_GET['id_projeto_corrente'] . "
                                              AND u.id_usuario != " . (int)$_GET['id_usuario_corrente'];

                                $query_user_selects = mysql_query($user_selects) or die("Erro ao enviar a query");
                                while ($result_user_selects = mysql_fetch_array($query_user_selects)) {
                                    ?>
                                    <option value="<?= $result_user_selects['id_usuario'] ?>">
                                        <?= $result_user_selects['login'] ?>
                                    </option>


                                    <?php
                                }
                                
 // Episode 2: Deleting User (s) of the project: the administrator selects the registered users
 // (Already existing) List of Participants (users that belong to this project)
 // And clicking the button ->.
                                ?>

                            </select>
                        </td>
                        <td>
                            <input name="usr_l2r" type="button" value="->">
                        </td>
                        <td rowspan="2">
                            <select  multiple name="usuarios_r" size="6">
                                <?php
                                $user_selects_nonparticipating = "SELECT id_usuario FROM participa where participa.id_projeto =" . (int)$_SESSION['id_projeto_corrente'];
                                $subqrr = mysql_query($user_selects_nonparticipating) or die("Erro ao enviar a subquery");
                                 if ($subqrr != 0) {
                                    $row = mysql_fetch_row($subqrr);
                                    $result_user_nonparticipating = "( $row[0]";
                                    while ($row = mysql_fetch_row($subqrr))
                                        $result_user_nonparticipating = "$result_user_nonparticipating , $row[0]";
                                    $result_user_nonparticipating = "$result_user_nonparticipating )";
                                }
                                $selection = "SELECT usuario.id_usuario, usuario.login FROM usuario 
                                    where usuario.id_usuario not in " .  mysql_real_escape_string($result_user_nonparticipating);
                              
                                echo($selection);
                                $query_user_nonparticipating = mysql_query($selection) or die("Erro ao enviar a query");
                                while ($result = mysql_fetch_array($query_user_nonparticipating)) {
                                    ?>
                                    <option value="<?= $result['id_usuario'] ?>">
                                        <?= $result['login'] ?>
                                    </option>

                                    <?php
                                }
                                
 // Episode 3: Including User (s) to the project: the administrator selects the registered users
 // (Already existing) from the list of users who do not belong to this project and
 // Clicking the button <-.
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>
                            <input name="usr_r2l" type="button" value="<-">
                        </td>
                    </tr>
    <?php
// Episodio 4: Para atualizar os relacionamentos realizados, o administrador clica no botao Atualizar.
    ?>
                    _egg_logo_guid()
                    <tr>
                        <td align="center" colspan="3" height="50" valign="bottom">
                            <input name="submit" type="submit" value="Atualizar">
                        </td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=rel_usuario.php">Veja o codigo fonte!</a></i>
          mysql_close($connect_database);
        </body>
    </html>

    <?php
}
?>
