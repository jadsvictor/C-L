<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");
include ("functionsProject/includeProject.php");


checkUserAuthentication("index.php");

// This script is only called when a solicitation of inclusion
// New project, or when a New User register on the system

// SCENARY - Register New Project
// Purpose: Allow user to register a new project
// Context: User want to include a new project in the database
// Precondition: Login
// Actors: User
// Resources: System, design data, database
// Episodes: The User clicks on the "add project" found in the top menu.
// The system provides a screen for the user to specify the details of the new project,
// As the project name and description.
// The user clicks the insert button.
// The system saves the new project in the database and automatically builds the navigation
// For this new project.
// Exception: If you specify a project name already exists and belongs or has participation
// This user, the system displays an error message.

// Called through the button to submit
if (isset($submit)) {

    $id_projeto_incluido = inclui_projeto($nome, $descricao);

    // Insert on index
    if ($id_projeto_incluido != -1) {
        $connect_database = bd_connect() or die("Erro ao conectar ao SGBD");
        $gerente = 1;
        $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
        $selection = "INSERT INTO participa (id_usuario, id_projeto, gerente)
                     VALUES ($id_usuario_corrente, $id_projeto_incluido, $gerente  )";
        mysql_query($selection) or die("Erro ao inserir na tabela participa");
    } 
    
    else {
        ?>
        <html>
            <title>Erro</title>
            <body>
                <p style="color: red; font-weight: bold; text-align: center">Nome de projeto j� existente!</p>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body>
        </html>   
        <?php
        return;
    }
    ?>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
}
 
else {
    ?>

    <html>
        <head>
            <title>Adicionar Projeto</title>
            <script language="javascript1.3">

                function chkFrmVals() {
                    if (document.forms[0].nome.value === "") {
                        alert('Preencha o campo "Nome"');
                        document.forms[0].nome.focus();
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        nOK = padrao.exec(document.forms[0].nome.value);
                        if (nOK)
                        {
                            window.alert("O nome do projeto n�o pode conter nenhum\n\
                                          dos seguintes caracteres:   / \\ : ? \" < > |");
                            document.forms[0].nome.focus();
                            return false;
                        }
                    }
                    return true;
                }

            </script>
        </head>
        <body>
            <h4>Adicionar Projeto:</h4>
            <br>
            <form action="" method="post" onSubmit="return chkFrmVals();">
                <table>
                    <tr>
                        <td>Nome:</td>
                        <td><input maxlength="128" name="nome" size="48" type="text"></td>
                    </tr>
                    <tr>
                        <td>Descri��o:</td>
                        <td><textarea cols="48" name="descricao" rows="4"></textarea></td>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" 
                                                                          type="submit" value="Adicionar Projeto"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_projeto.php">Veja o c�digo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
