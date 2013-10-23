<html>
<?php
session_start();

include("funcoes_genericas.php");
include ("functionsBD/verifica_gerente.php");
include("functionsBD/check_User_Authentication.php");
include ("functionsProject/check_proj_perm.php");

checkUserAuthentication("index.php");        

// Scenario: acess control
// Scenario - User chooses project
// Goal:  Allow User to choose a design.
// Context:  The user wants to choose a project
//            Pre-conditions: Login
// Actors:    User
// Means:  Projects
// Episodes: The user selects from the list of projects a project of which he is not
// Administrator.
// The user can:
// - Refresh scenario:
// - Update lexicon.

if (isset($_GET['id_projeto'])) {
    $project_id = (int)$_GET['id_projeto'];
}
?>

<script type="text/javascript1.3">

    function getIDPrj() {
        // combo-box de projeto
        var select = document.forms[0].id_projeto;
        // indice selecionado
        var indice = select.selectedIndex;
        // id_projeto correspondente ao indice
        var id_projeto = select.options[indice].value; 
        return id_projeto;

    }

    // loads the menu corresponding to the project
    function atualizaMenu() {   
        // To not do anything if they select the "- Select a Project -"
        if (!(document.forms[0].id_projeto.options[0].selected))
        {
            top.frames['code'].location.replace('code.php?id_projeto=' + getIDPrj());
            top.frames['text'].location.replace('main.php?id_projeto=' + getIDPrj());


            location.replace('heading.php?id_projeto=' + getIDPrj());
        } else {

            location.reload();
        }
        return false;
    }

<?php
// $id_projeto not only will joined if the first
// time the User is accessing the system
if (isset($project_id)) {   
    // Checking safety as $ id_projeto is passed through JavaScript (client)
    check_proj_perm($_SESSION['id_usuario_corrente'], $project_id) or die("Permissao negada");
    ?>

        function setPrjSelected() {
            var select = document.forms[0].id_projeto;
            for (var i = 0; i < select.length; i++) {
                if (select.options[i].value == <?= $project_id ?>) {
                    select.options[i].selected = true;
                    i = select.length;
                }
            }
        }

    <?php
}
?>

    function novoCenario() {
<?php

// Setting - Refresh Scenario
// Purpose: Allow Inclusion, Change and Delete a scenario by a user
// Context: User want to include a scenario not registered, change and / or delete
// A scenario previously registered.
// Precondition: Login
// Actors: User, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: The user clicks on the top menu option:
// If the user clicks in Include, then INCLUDE SCENARIO

if (isset($project_id)) {
    ?>
            var url = 'add_cenario.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
            var url = 'add_cenario.php?'
    <?php
}
?>


        var where = '_blank';
        var window_spec = 'dependent,height=600,width=550,resizable,scrollbars,titlebar';
        open(url, where, window_spec);
    }

    function novoLexico() {
<?php

// Scenarios - Upgrade Lexicon
// Purpose: Allow Inclusion, Change and Delete a Lexicon by user
// Context: User want to include a lexicon not registered, amend and / or
// Delete a scenario / lexicon previously registered.
// Precondition: Login
// Actors: User, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: The user clicks on the top menu option:
// If the user then clicks Add INCLUDE LEXICON

if (isset($project_id)) {
    ?>
            var url = 'add_lexico.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
            var url = 'add_lexico.php';
    <?php
}
?>

        var where = '_blank';
        var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
        open(url, where, window_spec);
    }

    function prjInfo(idprojeto) {
        top.frames['text'].location.replace('main.php?id_projeto=' + idprojeto);
    }

</script>

    <style>
        a
        {
            font-weight: bolder;
            color: Blue;
            font-family: Verdana, Arial;
            text-decoration: none
        }
        a:hover
        {
            font-weight: bolder;
            color: Tomato;
            font-family: Verdana, Arial;
            text-decoration: none
        }
    </style>
    <body style="background-color: #ffffff" 
           <?= (isset($project_id)) ? "onLoad=\"setPrjSelected();\"" : "" ?>>
        <form onSubmit="return atualizaMenu();">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr  style="background-color: #E0FFFF">
                   <td width="294" height="79" > <!--<img src="Images/Logo.jpg"></td>-->
                        <img src="Images/Logo_C.jpg" width="190" height="100"></td>
                    <td align="right" valign="top">
                        <table>
                            <tr>
                                <td align="right" valign="top"> <?php
                                    if (isset($project_id)) {

                                        $user_id = $_SESSION['id_usuario_corrente'];

                                        $ret = verificaGerente($user_id, $project_id);

                                        if ($ret != 0) {
                                            ?>
                                            <p style=" color: #FF0033">Administrador</p>


                                            <?php
                                        } else {
                                            ?>                               <p style=" color: #FF0033">Usuario normal</p>


                                            <?php
                                        }
                                    } else {
                                        ?>        

                                        <?php
                                    }
                                    ?>      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Projeto:&nbsp;&nbsp;

                                    <select name="id_projeto" size="1" onChange="atualizaMenu();">
                                        <option>-- Select a Project --</option>


                                        <?php
                                        
// ** Scenario "Login" **
// The system gives the user the option to register a new project
// Or use a project he is a part.
// Connect to the DBMS
                                        
                                       bd_connect() or die("Erro ao conectar ao SGBD");

// define the consult
                                        $selection = "SELECT p.id_projeto, p.nome, pa.gerente
      FROM usuario u, participa pa, projeto p
      WHERE u.id_usuario = pa.id_usuario
      AND pa.id_projeto = p.id_projeto
      AND pa.id_usuario = " . (int)$_SESSION["id_usuario_corrente"] . "
      ORDER BY p.nome";

// execute the consult
                                        $qrr = mysql_query($selection) or die("Erro ao executar query");

                                        while ($result = mysql_fetch_array($qrr)) {    // enquanto houver projetos
                                            ?>
                                            <option value="<?= $result['id_projeto'] ?>"><?= ($result['gerente'] == 1) ? "*" : "" ?>  <?= $result['nome'] ?></option>

                                            <?php
                                        }
                                        ?>          

                                    </select>&nbsp;&nbsp;
                                    <input type="submit" value="Atualizar">
                                </td>
                            </tr>
                            <tr bgcolor="#E0FFFF" height="15">

                            <tr bgcolor="#E0FFFF" height="30">

                                <td align="right" valign=MIDDLE> <?php
                                
// Scenario - choose Project Administrator
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Preconditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project doAdministrador
// Episodes: Appearing in the menu options:
//           -Add-Scenario (see Add Scenario);
//           -Add-Lexicon (see Add Lexicon);
//           -Info;
//           -Add-Project;
//           -Change Register.
                                
                                    
                                    // If the User has already chosen a project,
                                    //Then we can show links to add cen / lex
                                    // And informations (main page) of project
                                    if (isset($project_id)) { 
                                       
                                        ?> <a href="#" onClick="novoCenario();">Add Scenario</a>&nbsp;&nbsp;&nbsp; 
                                        <a href="#" onClick="novoLexico();">Adicionar S�mbolo</a>&nbsp;&nbsp;&nbsp; 
                                        <a href="#" title="Informa��es sobre o Projeto" onClick="prjInfo(<?= $project_id ?>);">Info</a>&nbsp;&nbsp;&nbsp; 
                                        <?php
                                    }
                                    ?> <?php
                                    
//Setting - Register New Project
// Purpose: Allow user to register a new project
// Context: User want to include a new project in the database
// Precondition: Login
// Actors: User
// Resources: System, design data, database
// Episodes: The User clicks on the "add project" found in the top menu.
                                    
                                    ?> <a href="#" onClick="window.open('add_projeto.php', '_blank', 'dependent,height=313,width=550,resizable,scrollbars,titlebar');">Adicionar 
                                        Projeto</a>&nbsp;&nbsp;&nbsp; <?php
                                        
// Scenario - Remove New Project
// Purpose: Allow Project Manager to remove a project
// Context: A Project Manager you want to remove a specific design based dadosF
// Precondition: Login Become administrator selected project.
// Actors: Administrator
// Resources: System, design data, database
// Episodes: The Administrator clicks on the "remove project" found in the top menu.


                                    if (isset($project_id)) {

                                        $user_id = (int)$_SESSION['id_usuario_corrente'];

                                        $ret = verificaGerente($user_id, $project_id);

                                        if ($ret != 0) {
                                            ?> <a href="#" onClick="window.open('remove_projeto.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">Remover 
                                                Projeto</a>&nbsp;&nbsp;&nbsp; <?php
                                        }
                                    }

// Scenario - Log into the system
// Purpose: Allow user to enter the system and choose a project he is
// Registered or registering new project
// Context: User System is open on the login screen of the system.
// User know your password User wishes to enter the system with your profile
// Precondition: User has accessed the system
// Actors: User, System
// Resource: Database
// Episodes: The system gives the user the options:
//          - CHANGE REGISTER, in which the user will be able to make changes to your registration

// Scneario - Change register
// Purpose: Allow user to make changes in your registration data
// Context: Open System, User have accessed the system and logged
// User want to change your registration
// Precondition: User has accessed the system
// Actors: User, System.
// Features: Interface
// Episodes: The user clicks the option to change the registration interface
                                    
                                    ?> <a href="#" onClick="window.open('Call_UpdUser.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">Alterar 
                                        Cadastro</a>&nbsp;&nbsp;&nbsp; 



                                    <a href="mailto:per@les.inf.puc-rio.br">Fale Conosco&nbsp;&nbsp;&nbsp;</a>

                                    <?php
                                    
// Scenario - Log into the system
// Purpose: Allow user to enter the system and choose a project he is
// Registered or registering new project
// Context: User System is open on the login screen of the system.
// User know your password User wishes to enter the system with your profile
// Precondition: User has accessed the system
// Actors: User, System
// Resource: Database
// Episodes: The system gives the user the options:
// - PERFORMING LOGOUT, ​​in which the user will be able to leave the
// Session and login again

// Scenario - Perform logout
// Purpose: Allow the user to perform the logout, maintaining the integrity of what was
// Done, and returns to the login screen
// Context: Open System. User has accessed the system.
// User wishes to exit the application and maintain the integrity of which was
// Done
// Precondition: User has accessed the system
// Actors: User, System.
// Features: Interface
// Episodes: The user clicks the logout option
                                    
                                    ?> <a href="logout.php" target="_parent");">Sair</a>&nbsp;&nbsp;&nbsp; <a href="ajuda.htm" target="_blank"> 
                                        Ajuda</a></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr height="33" bgcolor="#00359F" background="Images/FrameTop.gif">
                    <td background="Images/TopLeft.gif" width="294" valign="baseline"></td>
                    <td background="Images/FrameTop.gif" valign="baseline"></td>
                </tr>
            </table>
        </form>
    </body>
</html>
