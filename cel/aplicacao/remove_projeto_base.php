<?php
session_start();

include("funcoes_genericas.php");
include_once("CELConfig/CELConfig.inc");

$id_projeto = $_SESSION['id_projeto_corrente'];

removeProjeto($id_projeto);
?>
<html>
    <script language="javascript1.3">
        function logoff()
        {
            location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
        }
    </script>
    <head>
        <title>Remover Projeto</title>
    </head>  

    <body>
    <center><b>Projeto apagado com sucesso.</b></center>   
    <p>
        <a href="javascript:logoff();">Clique aqui para Sair</a>
    </p>
    <p>
        <i><a href="showSource.php?file=remove_projeto_base.php">Veja o codigo fonte!</a></i> 
    </p>
</body>
</html>

