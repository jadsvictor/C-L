<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
chkUser("index.php");  

$id_projeto = 0;
$id_lexico = 0;
inserirPedidoRemoverLexico($id_projeto, $id_lexico, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacao efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
