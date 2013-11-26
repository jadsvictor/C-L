<html>
    <head>
        <title>Remover Projeto</title>

    
        <?php
// Setting - Remove the base design
// Purpose: Perform removal of a project database
// Context: A Project Management want to remove a particular project database
// Preconditions: Login Become the selected project administrator,
// Get the selected project for removal in remove_projeto.php.
// Actors: Administrator
// Resource: System design data, database
// Episodes: The system deletes all data relating to your particular project database.

        session_start();
        
        include("functionsProject/remove_projeto.php");
        include("funcoes_genericas.php");
        include_once("CELConfig/CELConfig.inc");

        $id_project = $_SESSION['id_projeto_corrente'];

        removeProjeto($id_project);
        ?>

        <script type ="text/javascript1.3">
            function logoff()
            {
                location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
            }
        </script>
    </head>  

    <body>
        <h1 style="text-align:center;"><b>Projeto apagado com sucesso.</b></h1>   
        <p>
            <a href="javascript:logoff();">Clique aqui para Sair</a>
        </p>
        <p>
            <i><a href="showSource.php?file=remove_projeto_base.php">Veja o codigo fonte!</a></i> 
        </p>
    </body>
</html>

