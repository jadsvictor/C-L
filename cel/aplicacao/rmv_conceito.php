<?php
// Cen�rio -  Excluir Conceito 
//Objetivo:	Permitir ao Usuário Excluir um conceito que esteja ativo
//Contexto:	Usuário deseja excluir um conceito
//              Pré-Condição: Login, Cen�rio cadastrado no sistema
//Atores:	Usuário, Sistema
//Recursos:	Dados informados
//Episódios:	O sistema fornecerá uma tela para o usuário justificar a necessidade daquela
//              exclusão para que o administrador possa ler e aprovar ou não a mesma.
//              Esta tela também conterá um botão para a confirmação da exclusão.
//Restrição:    Depois de clicar no botão, o sistema verifica se todos os campos foram preenchidos 
//Exceção:	Se todos os campos não foram preenchidos, retorna para o usuário uma mensagem
//              avisando que todos os campos devem ser preenchidos e um botão de voltar
//              para a pagina anterior.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");  

$id_concept = 0;
inserirPedidoRemoverConceito($_SESSION['id_projeto_corrente'], $id_concept, $_SESSION['id_usuario_corrente']);
?>  

<script type="text/javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacao efetuada com sucesso!</h4>

<script type="text/javascript1.3">

    self.close();

</script>
