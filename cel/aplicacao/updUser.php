<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

$connect_database = bd_connect() or die("Erro ao conectar ao SGBD");
if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
    echo "SUCESSO NA CONEXAO AO BD <br>";
else
    echo "ERRO NA CONEXAO AO BD <br>";
?>

<html>
    <head>
        <title>Alterar dados de Usuario</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>


    <body>

        <?php
// Scenario - Changing registration
//
// Purpose: Allow User to make alteration in their records
// Context: Open System, User have accessed the system and logged
// User want to change your registration
// Pre-Condition: User has accessed the system
// Actors: User, System.
// Features: Interface
// Episodes: The User alters the desired data
// User clicks the button to update

        $password_cript = md5($password);

        //test if a variable has the correct type
        assert(is_string($password_cript));

        mysql_query("UPDATE usuario SET  nome = ' " . mysql_real_escape_string($_GET["nome"]) . "'" . "
        , login = ' " . mysql_real_escape_string($_GET["login"]) . "'" . " 
        , email = ' " . mysql_real_escape_string($_GET["email"]) . "'" . " 
        , senha = '$password_cript' 
         WHERE  id_usuario=" . (int) $_GET["id_usuario"]) or die("<p style='color: red; font-weight: bold; text-align: 
         center'>Erro!Login ja existente!</p><br><br><center><a 
         href='JavaScript:window.history.go(-1)'>Voltar</a></center>");
        ?>

    <center><b>Cadastro atualizado com sucesso!</b></center>
    <center><button onClick="javascript:window.close();">Fechar</button></center>

    mysql_close($connect_database);
</body>
</html>