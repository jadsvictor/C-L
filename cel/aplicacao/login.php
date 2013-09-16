<?php
//Title: Acess the system
//Goal: Allow the user to access the Application of Lexical Editing and Editing
//Scenarios, register or order in the system password in the case of having 
//forgotten it.
//Context: A página da aplicação é acessada. Na página de abertura ../cel/aplicacao/login.php 
//o usuário insere login ou senha incorretos - $wrong=true.
//Actors: user, application
//Means: UURL to access the system, login, password, bd.inc, httprequest.inc, 
//$ wrong, $ url, showSource.php? File = login.php, esqueciSenha.php, add_usuario.php? 
//Again = true
//Episode 1: login

session_start();

include("bd.inc");

$url = '';
$submit = '';
$login = '';
$password = '';
$wrong = "false";

include("httprequest.inc");

// Episode 2: Connect the DBMS
// Constraint: a function defined in bd_connect bd.inc used
// Exception: Failed to connect database

$database_conection = bd_connect() or die("Erro ao conectar ao SGBD");

// Episode 9: If the form has been submitted then check if the login and
// password are correct.

if ($submit == 'Entrar') {
    $cript_password = md5($password);
    $selection = "SELECT id_usuario FROM usuario WHERE login='$login' AND senha='$cript_password'";
    $qrr = mysql_query($selection) or die("Erro ao executar a query");

// Episode 10: If the login and / or password is incorrect then return the page
// login with wrong = true in the URL.
        
    if (!mysql_num_rows($qrr)) {
        ?>
        <script language="javascript1.3">
            document.location.replace('login.php?wrong=true&url=<?= $url ?>');
        </script>

        <?php
        $wrong = $_get["wrong"];
    }

// Episode 11: If the login and password are correct then register session for
// the user to close the application and open login.php
    
    else {

        $row = mysql_fetch_row($qrr);
        //$id_usuario_corrente = $row[0];

        //session_register("id_usuario_corrente");
        $_SESSION['id_usuario_corrente'] = $row[0];
        ?>
        <script language="javascript1.3">
            opener.document.location.replace('<?= $url ?>');
            self.close();
        </script>

        <?php
    }
}

//Episode 3: Show the login form to user.

else {
    ?>

    <html>
        <head>
            <title>Entry with your Login and Password</title>
        </head>
        <body>

    <?php
    
    //Episode 4: If wrong = true then display the message "Incorrect Login or Password"
    if ($wrong == "true") {
        ?>

                <p style="color: red; font-weight: bold; text-align: center">
                    <img src="Images/Logo_CEL.jpg" width="180" height="180"><br/><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;Incorrect Login or Password</p>

                <?php
            }
            //Episodio 5: If wrong! = True then display the message "Enter your login and password"
            
            else {
                ?>

                <p style="color: green; font-weight: bold; text-align: center">
                    <img src="Images/Logo_CEL.jpg" width="100" height="100"><br/><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;Entry with your Login and Password:</p>

                <?php
            }
            ?>

            <form action="?url=<?= $url ?>" method="post">
                <div align="center">
                    <table cellpadding="5">
                        <tr><td>Login:</td><td><input maxlength="32" name="login" size="24" type="text"></td></tr>
                        <tr><td>Senha:</td><td><input maxlength="32" name="senha" size="24" type="password"></td></tr>
                        <tr><td height="10"></td></tr>
                        <tr><td align="center" colspan="2"><input name="submit" type="submit" value="Entrar"></td></tr>
                    </table>

    <?php 
                    //Episode 6: [REGISTER NEW USER]
    ?>
                    <p><a href="add_usuario.php?novo=true">Sign in</a>&nbsp;&nbsp;

    <?php 
                        //Episode 7: [REMEMBER PASSWORD]
    ?>
                        <a href="esqueciSenha.php">Forgot password</a></p>
                </div>
            </form>
        </body>

                    <?php 
         //Episode 8: [SHOW THE SOURCE]
                    ?>

        <i><a href="showSource.php?file=login.php">See the source!</a></i>    
    </html>

    <?php
}
?>