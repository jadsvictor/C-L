<?php
session_start();
?>

<?php
include("funcoes_genericas.php");
include_once("bd.inc");

$primeira_vez = "true";

include("httprequest.inc");

if (isset($submit)) {
    $primeira_vez = "false";
    if ($nome == "" || $email == "" || $login == "" || $password == "" || $senha_conf == "") {
        $p_style = "color: red; font-weight: bold";
        $p_text = "Por favor, preencha todos os campos.";
        recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&senha=$password&senha_conf=$senha_conf&novo=$novo");
    } else {
        if ($password != $senha_conf) {
            $p_style = "color: red; font-weight: bold";
            $p_text = "Senhas diferentes. Favor preencher novamente as senhas.";
            recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&novo=$novo");
        } else {

// Scenary - Include independent user
// Purpose: Allow a user who is not registered as an administrator, register
// With the administrator profile
// Context: Open System User wishes to subscribe to the system as administrator.
// User in the registration screen user
// Precondition: User has accessed the system
// Actors: User, System
// Features: Interface, Database
// Episodes: The system returns to the user interface with fields for entering
// A Name, email, login, password, and password confirmation.
// The user fills in the fields and click on submit
// The system then checks to see if all fields are filled.
// If a field fails to be completed, the system warns you that all
// Fields must be filled.
// If all fields are completed, the system checks in the bank
// Data to see if this login exists ..
// If you enter that login already exists, the system returns the same page
// To the user warning that the user must choose another login.
            $database_conection = bd_connect() or die("Erro ao conectar ao SGBD");
            $selection = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $qrr = mysql_query($selection) or die("Erro ao enviar a query");
            if (mysql_num_rows($qrr)) {
            	
// scenary - Add User
// Purpose: Allow the administrator to create new users.
// Context: The administrator wants to add new users (not registered)
// Create new users to the selected project.
// Preconditions: Login
// Actors: Administrator
// Resources: User Data
// Episodes: The Administrator clicks on the link "Add User (nonexistent) this project,"
// Entering the new user information: name, email, login and password.
// If the login already exists, an error message appears on the screen stating that
// This login already exists.
                ?>
                <script language="JavaScript">
                    alert("Login j� existente no sistema. Favor escolher outro login.")
                </script>

                <?php
                recarrega("?novo=$novo");
            } else {
                $nome = str_replace(">", " ", str_replace("<", " ", $nome));
                $login = str_replace(">", " ", str_replace("<", " ", $login));
                $email = str_replace(">", " ", str_replace("<", " ", $email));
                $password = md5($password);
                $selection = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$nome', '$login', '$email', '$password')";
                mysql_query($selection) or die("Erro ao cadastrar o usuario");
                recarrega("?cadastrado=&novo=$novo&login=$login");
            }
        }
    }
} elseif (isset($cadastrado)) {
    if ($novo == "true") {    
    	
// scenary - Include independent user
// Purpose: Allow a user who is not registered as an administrator, register
// With the administrator profile
// Context: Open System User wishes to subscribe to the system as administrator.
// User in the registration screen user
// Precondition: User has accessed the system
// Actors: User, System
// Features: Interface, Database
// Episodes: If you enter that login does not exist, the system registers this user
// As an administrator in the database, enabling:
// - Redirect it to interface REGISTER NEW PROJECT;
        $_SESSION['id_usuario_corrente'] = simple_query("id_usuario", "usuario", "login = '$login'");
        ?>

        <script language="javascript1.3">

            opener.location.replace('index.php');
            open('add_projeto.php', '', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');
            self.close();


        </script>

        <?php
    } else {

        
        $database_conection = bd_connect() or die("Erro ao conectar ao SGBD");
        $id_usuario_incluido = simple_query("id_usuario", "usuario", "login = '$login'");
        $selection = "INSERT INTO participa (id_usuario, id_projeto)
          VALUES ($id_usuario_incluido, " . $_SESSION['id_projeto_corrente'] . ")";
        mysql_query($selection) or die("Erro ao inserir na tabela participa");

        $nome_usuario = simple_query("nome", "usuario", "id_usuario = $id_usuario_incluido");
        $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
        ?>

        <script language="javascript1.3">

            document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usu�rio <b><?= $nome_usuario ?></b> cadastrado e inclu�do no projeto <b><?= $project_name ?></b></p>');
            document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        </script>

        <?php
    }
} else {  
    if (empty($p_style)) {
        $p_style = "color: green; font-weight: bold";
        $p_text = "Favor preencher os dados abaixo:";
    }

    if (true) {
        $email = "";
        $login = "";
        $nome = "";
        $password = "";
        $senha_conf = "";
    }
    ?>

    <html>
        <head>
            <title>Cadastro de Usu�rio</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        </head>
        <body>
            <script language="JavaScript">
                <!--
                function verifyEmail(form)
                {
                    email = form.email.value;
                    i = email.indexOf("@");
                    if (i === -1)
                    {
                        alert('Aten��o: o E-mail digitado n�o � v�lido.');
                        return false;
                    }
                }

                function checkEmail(email) {
                    if (email.value.length > 0)
                    {
                        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
                        {
                            return (true);
                        }
                        alert("Aten��o: o E-mail digitado n�o � v�lido.");
                        email.focus();
                        email.select();
                        return (false);
                    }
                }

                //-->



            </SCRIPT>

            <p style="<?= $p_style ?>"><?= $p_text ?></p>
            <form action="?novo=<?= $novo ?>" method="post">
                <table>
                    <tr>
                        <td>Nome:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?= $nome ?>"></td>
                    </tr>
                    <tr>
                        <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?= $email ?>" OnBlur="checkEmail(this);"></td>
                    </tr>
                    <tr>
                        <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?= $login ?>"></td>
                    </tr>
                    <tr>
                        <td>Senha:</td><td><input name="senha" maxlength="32" size="16" type="password" value="<?= $password ?>"></td>
                        <td>Senha (confirma��o):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
                    </tr>
                    <tr>

                        <?php
                        
// Scenary - Add User
// Purpose: Allow the administrator to create new users.
// Context: The administrator wants to add new users (not registered) creating new
// Users to the selected project.
// Preconditions: Login
// Actors: Administrator
// Resources: User Data
// Episodes: By clicking the Register button to confirm the addition of the new
// User to the selected project.
// The newly created user will receive a message via email with your login and password.
                        ?>

                        <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_usuario.php">Veja o c�digo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
