<html>
    <head>
        <title>Esqueci minha senha</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
        <!--
        function TestarBranco(form)
        {
            login = form.login.value;

            if ((login === ""))
            {
                alert("Por favor, digite o seu Login.");
                form.login.focus();
                return false;
            }

        }

    </SCRIPT>
    <p style="color: red; font-weight: bold; text-align: center">
        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
    </p>

    <body bgcolor="#FFFFFF">
        <form action="enviar_senha.php" method="post">
            <div align="center">

                <?php
                ?>

                <p style="color: green; font-weight: bold; text-align: center">Entre com seu Login:</p>

                <table cellpadding="5">
                    <tr><td>Login:</td><td><input maxlength="12" name="login" size="24" type="text"></td></tr>

                    <tr><td height="10"></td></tr>
                    <tr><td align="center" colspan="2"><input name="submit"  onClick="return TestarBranco(this.form);" type="submit" value="Enviar"></td></tr>
                </table>
            </div>
            <br>
            <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </form<i><a href="showSource.php?file=esqueciSenha.php">Veja o c�digo fonte!</a></i>
    </body>
</html>
