<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

$id_usuario = $_SESSION['id_usuario_corrente'];

$r = bd_connect() or die("Erro ao conectar ao SGBD");
?>

<html>
    <head>
        <title>Alterar dados de Usuario</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>


    <body>

<?php
// Cenario - Alterar cadastro
//
//Objetivo:  Permitir ao usuario realizar alteracao nos seus dados cadastrais	
//Contexto:  Sistema aberto, Usuario ter acessado ao sistema e logado 
//           Usuario deseja alterar seus dados cadastrais 
//           Pre-Condicao: Usuario ter acessado ao sistema	
//Atores:    Usuario, Sistema.	
//Recursos:  Interface	
//Episodios: O usuario altera os dados desejados
// 	     Usuario clica no botao de atualizar

$password_cript = md5($senha);
$new_data = "UPDATE usuario SET  nome ='$nome' , login = '$login' , email = '$email' , senha = '$password_cript' WHERE  id_usuario='$id_usuario'";

mysql_query($new_data) or die("<p style='color: red; font-weight: bold; text-align: 
    center'>Erro!Login ja existente!</p><br><br><center><a 
    href='JavaScript:window.history.go(-1)'>Voltar</a></center>");
?>

    <center><b>Cadastro atualizado com sucesso!</b></center>
    <center><button onClick="javascript:window.close();">Fechar</button></center>


</body>
</html>