<?php
//Cenário  -  Remover Projeto 

//Objetivo:	   Permitir ao Administrador do projeto remover um projeto
//Contexto:	   Um Administrador de projeto deseja remover um determinado projeto da base de dados
//                 Pré-Condição: Login, Ser administrador do projeto selecionado.  
//Atores:	   Administrador
//Recursos:	   Sistema, dados do projeto, base de dados

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("bd.inc");

?>
<html>
    <head>
        <title>Remover Projeto</title>
    </head>
<?php

$connect_database = bd_connect() or die("Erro ao conectar ao SGBD");
if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXAO AO BD <br>";
        else
            echo "ERRO NA CONEXAO AO BD <br>";
        
$query_select_project = mysql_query("SELECT * FROM projeto WHERE id_projeto =" . (int)$_GET['id_projeto_corrente']) or die("Erro ao enviar a query de select no projeto");
$resultArrayProject = mysql_fetch_array($query_select_project);
$name_Project = $resultArrayProject[1];
$date_Project = $resultArrayProject[2];
$project_description = $resultArrayProject[3];

//Episodio 1:   O Administrador clica na opcao remover projeto encontrada no menu superior.
?>    
    <body>
        <h4>Remover Projeto:</h4>

        <p><br>
        </p>
       table{
        width="100%" border="0";
       }
       td{
        width="29%";
       }
<?php
// Episodio 2: O sistema disponibiliza uma tela para o administrador ter certeza 
// de que esta removendo o projeto correto.
?>
       _egg_logo_guid()
        <table>
            <tr> 
                <td><b>Nome do Projeto:</b></td>
                <td><b>Data de criacao do projeto;o</b></td>
                <td><b>Descricao do projeto;o</b></td>
            </tr>
            <tr> 
                <td><?php echo $name_Project; ?></td>
                <td><?php echo $date_Project; ?></td>
                <td><?php echo $project_description; ?></td>
            </tr>
        </table>
        <br><br>
<?php
//Episodio 3:  O Administrador clica no link de remoção.
?>
  <h1 style="text-align:center;">Cuidado!O projeto sera apagado para todos seus usuarios!</h1>
    <p><br>
    </p>
<?php
//Episodio 4: O sistema chama a página que removerá o projeto do banco de dados.
?>
    <h1 style="text-align:center;"><a href="remove_projeto_base.php">Apagar o projeto</a></h1> 
    <p>
    <i><a href="showSource.php?file=remove_projeto.php">Veja o codigo fonte!</a></i> 
    </p>
    mysql_close($connect_database);
</body>
</html>

