<?php
//Cenários -  Excluir Léxico 
//Objetivo:	Permitir ao Usuário Excluir uma palavra do léxico que esteja ativa
//Contexto:	Usuário deseja excluir uma palavra do léxico
//              Pré-Condição: Login, palavra do léxico cadastrada no sistema 
//Atores:	Usuário, Sistema
//Recursos:	Dados informados
//Episódios:	O sistema fornecerá uma tela para o usuário justificar a necessidade
//              daquela exclusão para que o administrador possa ler e aprovar ou não.
//              Esta tela também conterá um botão para a confirmação da exclusão.
//Restrição:    Depois de clicado o botão o sistema verifica se todos os campos foram preenchidos 
//Exceção:	Se todos os campos não foram preenchidos, retorna para o usuário 
//              uma mensagem avisando que todos os campos devem ser preenchidos 
//              e um botão de voltar para a pagina anterior.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsLexic/inserir_Pedido_Remover_Lexico.php");

chkUser("index.php");  

$id_project = 0;
$id_lexicon = 0;
inserirPedidoRemoverLexico($id_project, $id_lexicon, $_SESSION['id_usuario_corrente']);
?>  

<script type="text/javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacao efetuada com sucesso!</h4>

<script type="text/javascript1.3">

    self.close();

</script>
