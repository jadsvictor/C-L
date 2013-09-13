<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
chkUser("index.php");  

$id_cenario = 0;
inserirPedidoRemoverCenario($_SESSION['id_projeto_corrente'], $id_cenario, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacaoo efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
