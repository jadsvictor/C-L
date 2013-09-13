<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

?>
<html>
    <head>
        <title>Remover Projeto</title>
    </head>
<?php
$id_projeto = $_SESSION['id_projeto_corrente'];

$r = bd_connect() or die("Erro ao conectar ao SGBD");
$seleciona_projeto = "SELECT * FROM projeto WHERE id_projeto = '$id_projeto' ";
$query_seleciona_projeto = mysql_query($seleciona_projeto) or die("Erro ao enviar a query de select no projeto");
$resultArrayProjeto = mysql_fetch_array($query_seleciona_projeto);
$nome_Projeto = $resultArrayProjeto[1];
$data_Projeto = $resultArrayProjeto[2];
$descricao_Projeto = $resultArrayProjeto[3];
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
        <table>
            <tr> 
                <td><b>Nome do Projeto:</b></td>
                <td><b>Data de criacao do projeto;o</b></td>
                <td><b>Descricao do projeto;o</b></td>
            </tr>
            <tr> 
                <td><?php echo $nome_Projeto; ?></td>
                <td><?php echo $data_Projeto; ?></td>
                <td><?php echo $descricao_Projeto; ?></td>
            </tr>
        </table>
        <br><br>
    <center><b>Cuidado!O projeto sera apagado para todos seus usuarios!</b></center>
    <p><br>
    </p>
    <center><a href="remove_projeto_base.php">Apagar o projeto</a></center> 
    <p>
    <i><a href="showSource.php?file=remove_projeto.php">Veja o codigo fonte!</a></i> 
    </p>
</body>
</html>

