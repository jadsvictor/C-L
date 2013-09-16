<?php
session_start();
include_once("CELConfig/CELConfig.inc");

//$_SESSION['site'] = 'http://pes.inf.puc-rio.br/pes03_1_1/Site/desenvolvimento/teste/';       
//$_SESSION['site'] = 'http://139.82.24.189/cel_vf/aplicacao/teste/';
/* URL of the directory containing the files DAML */
$_SESSION['site'] = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

//$_SESSION['diretorio'] = "/home/local/pes/pes03_1_1/Site/desenvolvimento/teste/";        
//$_SESSION['diretorio'] = "teste/";        
/* Relative path to the directory containing the CEL files DAML */
$_SESSION['diretorio'] = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("coloca_links.php");


// Checks if the user has been authenticated
chkUser("index.php");

//Parameter receives the heading.php. No it will lock that variable not already 
//been initialized
if (isset($_GET['id_projeto'])) {
    $project_id = $_GET['id_projeto'];
} else {
    // $id_projeto = ""; 
}

if (!isset($_SESSION['id_projeto_corrente'])) {

    $_SESSION['id_projeto_corrente'] = "";
}
?>    

<html> 



    <head> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <script language="javascript1.3">

            // Functions that will be used when the script is called through his own or tree 
            function reCarrega(URL) {
                document.location.replace(URL);
            }

<?php
// Scenario - Refresh Scenario
// Purpose: Allow Inclusion, Change and Delete a scenario by a user
// Context: User want to include a scenario not registered, change and / or delete
// A scenario previously registered.
// Precondition: Login
// Actors: User, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: The user clicks on the top menu option:
// If the user, clicks Change then CHANGE SCENARIO
?>

            function altCenario(cenario) {
                var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Refresh Scenario
// Purpose: Allow Inclusion, Change and Delete a scenario by a user
// Context: User want to include a scenario not registered, change and / or delete
// A scenario previously registered.
// Precondition: Login
// Actors: User, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: The user clicks on the top menu option:
// If user clicks Delete then DELETE SCENE
?>

            function rmvCenario(cenario) {
                var url = 'rmv_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenarios - Upgrade Lexicon
// Purpose: Allow Included, Excluded Alteraoe a Lexicon for a user
// Context: Usurio want to include a lexicon still in the register, amend and / or
// Delete a scenario / lexicon previously registered.
// Pre-condition: Login
// Actors: Usurio, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: the user clicks on the top menu option:
// If user, click Change then CHANGE lexicon
?>

            function altLexico(lexico) {
                var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenarios - Upgrade Lexicon
// Purpose: Allow Inclusion, Change and Delete a Lexicon by user
// Context: User want to include a lexicon not registered, amend and / or
// Delete a scenario / lexicon previously registered.
// Precondition: Login
// Actors: User, Project Manager
// Resources: System, top menu, the object to be modified
// Episodes: The user clicks on the top menu option:
// If user clicks Delete then DELETE LEXICON
?>

            function rmvLexico(lexico) {
                var url = 'rmv_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            //Functions that will be used when the script is invoked through the heading.php 

<?php
// Scenario - Upgrade Scenario
// Purpose: Allow Included, Excluded Alteraoe a Scenario for a user
// Context: Usurio want to include a scenario in yet registered, change and / or delete
// One scenario previously registered.
// Pre-condition: Login
// Actors: Usurio, Project Manager
// Resources: System, top menu, the object to be modified
// Episdios: the user clicks on the top menu option:
// If user click Change then CHANGE scenario
?>

            function altConceito(conceito) {
                var url = 'alt_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Upgrade Concept
// Purpose: Allow Included, Excluded Alteraoe a Scenario for a user
// Context: Usurio want to include a scenario in yet registered, change and / or delete
// One scenario previously registered.
// Pre-condition: Login
// Actors: Usurio, Project Manager
// Resources: System, top menu, the object to be modified
// Episdios: the user clicks on the top menu option:
// If user click Delete then DELETE scenario
?>

            function rmvConceito(conceito) {
                var url = 'rmv_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function rmvRelacao(relacao) {

                var url = 'rmv_relacao.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_relacao=' + relacao;
                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Preconditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project Admnistrator
// Episodes: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing onscreen options:
// Check-ordered change of scenerio (see Check applications change scenario);
?>

            function pedidoCenario() {
<?php
if (isset($project_id)) {
    ?>
                    var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
                    var url = 'ver_pedido_cenario.php';
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
//  Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project Administrator
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// - Check requests for alteration of terms of the lexicon
// (Check to see requests for alteration of terms of the lexicon);
?>

            function pedidoLexico() {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
                    var url = 'ver_pedido_lexico.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project Administrator
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// - Check requests for alteration of terms of the lexicon
// (Check to see requests for alteration of terms of the lexicon);
?>

            function pedidoConceito() {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'ver_pedido_conceito.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
                    var url = 'ver_pedido_conceito.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function pedidoRelacao() {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'ver_pedido_relacao.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
                    var url = 'ver_pedido_relacao.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project doAdministrador
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// Add-usurio (at present) in this project (see Add Usurio);
?>

            function addUsuario() {
                var url = 'add_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project doAdministrador
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// J-Relate existing users with this design
// (See Relate users with projects);
?>

            function relUsuario() {
                var url = 'rel_usuario.php';
                var where = '_blank';
                var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project doAdministrador
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// Generate-xml this project (see Generate XML reports); 
?>

            function geraXML()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'form_xml.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} else {
    ?>
                    var url = 'form_xml.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function recuperaXML()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'recuperarXML.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} 
else {
    ?>
                    var url = 'recuperarXML.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

            function geraGrafo()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'gerarGrafo.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} 
else {
    ?>
                    var url = 'gerarGrafo.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }


<?php
// Ontology
// Goal: Generate project ontology
?>
            function geraOntologia()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'inicio.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} 
else {
    ?>
                    var url = 'inicio.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = "";
                open(url, where, window_spec);
            }

<?php
// Ontology - DAML
// Purpose: Generate daml ontology project
?>
            function geraDAML()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'form_daml.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} 
else {
    ?>
                    var url = 'form_daml.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=375,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }

<?php
// Purpose: Retrieve historical ontology in DAML
?>
            function recuperaDAML()
            {

<?php
if (isset($project_id)) {
    ?>
                    var url = 'recuperaDAML.php?id_projeto=' + '<?= $project_id ?>';
    <?php
} 
else {
    ?>
                    var url = 'recuperaDAML.php?'
    <?php
}
?>

                var where = '_blank';
                var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                open(url, where, window_spec);
            }


        </script> 
        <script type="text/javascript" src="mtmtrack.js">
        </script> 
    </head> 
    <body> 

        <!--                     First Part                                     --> 

<?php
include("frame_inferior.php");

// SCRIPT CALLED BY OWN main.php (OR THE TREE)
if (isset($id) && isset($t)) {      
    $vetorVazio = array();
    if ($t == "c") {
        print "<h3>Informa��es sobre o cen�rio</h3>";
    } elseif ($t == "l") {
        print "<h3>Informa��es sobre o s�mbolo</h3>";
    } elseif ($t == "oc") {
        print "<h3>Informa��es sobre o conceito</h3>";
    } elseif ($t == "or") {
        print "<h3>Informa��es sobre a rela��o</h3>";
    } elseif ($t == "oa") {
        print "<h3>Informa��es sobre o axioma</h3>";
    }
    ?>    
            <table> 




                <!--                     SECOND PART                         --> 


    <?php
    $c = bd_connect() or die("Erro ao conectar ao SGBD");
    ?>   



                <!-- SCENARIO --> 

    <?php
    // if scenario 
    if ($t == "c") {        
        $selection = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_projeto    
              FROM cenario    
              WHERE id_cenario = $id";

        $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        $result = mysql_fetch_array($qrr);

        $c_project_id = $result['id_projeto'];
        
        // load vector scenery
        $scenarios_vector = carrega_vetor_cenario($c_project_id, $id, true); 
        quicksort($scenarios_vector, 0, count($scenarios_vector) - 1, 'cenario');
        
        // load vector lexicons
        $lexicons_vector = carrega_vetor_lexicos($c_project_id, 0, false); 
        quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');
        ?>    

                    <tr> 
                        <th>Titulo:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['titulo'], $lexicons_vector, $vetorVazio)); ?>
                        </td> 

                    </tr> 
                    <tr> 
                        <th>Objetivo:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['objetivo'], $lexicons_vector, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Contexto:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['contexto'], $lexicons_vector, $scenarios_vector)); ?>		 
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Atores:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['atores'], $lexicons_vector, $vetorVazio)); ?>
                        </td>  
                    </tr> 
                    <tr> 
                        <th>Recursos:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['recursos'], $lexicons_vector, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Exce��o:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['excecao'], $lexicons_vector, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Epis�dios:</th><td CLASS="Estilo">
        <?php echo nl2br(monta_links($result['episodios'], $lexicons_vector, $scenarios_vector)); ?>

                        </td> 
                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE> 
                            <a href="#" onClick="altCenario(<?= $result['id_cenario'] ?>);">Alterar Cen�rio</a> 
                            </th> 
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="rmvCenario(<?= $result['id_cenario'] ?>);">Remover Cen�rio</a> 
                            </th> 
                    </tr> 


                    <!-- LEXICON --> 

                <?php
            } elseif ($t == "l") {

                $selection = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto    
              FROM lexico    
              WHERE id_lexico = $id";

                $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                $result = mysql_fetch_array($qrr);

                $l_project_id = $result['id_projeto'];

                $lexicons_vector = carrega_vetor_lexicos($l_project_id, $id, true);

                quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');
                ?>    
                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?php echo $result['nome']; ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>No��o:</th><td CLASS="Estilo"><?php echo nl2br(monta_links($result['nocao'], $lexicons_vector, $vetorVazio)); ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Classifica��o:</th><td CLASS="Estilo"><?= nl2br($result['tipo']) ?>
                        </td> 
                    </tr> 
                    <tr> 
                        <th>Impacto(s):</th><td CLASS="Estilo"><?php echo nl2br(monta_links($result['impacto'], $lexicons_vector, $vetorVazio)); ?> 
                        </td>
                    </tr> 
                    <tr> 
                        <th>Sin�nimo(s):</th> 

                    <?php
                    //sinonimos 
                    $project_id = $_SESSION['id_projeto_corrente'];
                    $qSynonymous = "SELECT * FROM sinonimo WHERE id_lexico = $id";
                    $qrr = mysql_query($qSynonymous) or die("Erro ao enviar a query de Sinonimos" . mysql_error());

                    $tempS = array();

                    while ($resultSynonymous = mysql_fetch_array($qrr)) {
                        $tempS[] = $resultSynonymous['nome'];
                    }
                    ?>    

                        <td CLASS="Estilo">

                            <?php
                            $count = count($tempS);

                            for ($i = 0; $i < $count; $i++) {
                                if ($i == $count - 1) {
                                    echo $tempS[$i] . ".";
                                } else {
                                    echo $tempS[$i] . ", ";
                                }
                            }
                            ?>    

                        </td> 

                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign="middle"> 
                            <a href="#" onClick="altLexico(<?= $result['id_lexico'] ?>);">Alterar S�mbolo</a> 
                            </th> 
                        <td CLASS="Estilo" valign="middle"> 
                            <a href="#" onClick="rmvLexico(<?= $result['id_lexico'] ?>);">Remover S�mbolo</a> 
                            </th> 
                    </tr> 


                    <!-- ONTOLOGIA - CONCEITO --> 

        <?php
    } elseif ($t == "oc") {        // se for cenario 
        $selection = "SELECT id_conceito, nome, descricao   
              FROM   conceito   
              WHERE  id_conceito = $id";

        $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        $result = mysql_fetch_array($qrr);
        ?>    

                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td> 
                    </tr> 
                    <tr> 
                        <th>Descri��o:</th><td CLASS="Estilo"><?= nl2br($result['descricao']) ?></td> 
                    </tr> 
                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE>                     
                            </th> 
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="rmvConceito(<?= $result['id_conceito'] ?>);">Remover Conceito</a> 
                            </th> 
                    </tr> 




                    <!-- ONTOLOGY - RELATIONS --> 

                    <?php
                    
                  // se for cenario 
                } elseif ($t == "or") {        
                    $selection = "SELECT id_relacao, nome   
              FROM relacao   
              WHERE id_relacao = $id";
                    $qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    $result = mysql_fetch_array($qrr);
                    ?>    

                    <tr> 
                        <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td> 
                    </tr> 

                </TABLE> 
                <BR> 
                <TABLE> 
                    <tr> 
                        <td CLASS="Estilo" height="40" valign=MIDDLE>                   
                            </th>
                        <td CLASS="Estilo"  valign=MIDDLE> 
                            <a href="#" onClick="rmvRelacao(<?= $result['id_relacao'] ?>);">Remover Rela��o</a> 
                            </th> 
                    </tr> 




                        <?php
                    }
                    ?>   

            </table> 
            <br> 


            <!--                     THIRD PART                             --> 


                        <?php
                        if ($t == "c") {
                            print "<h3>Cen�rios que referenciam este cen�rio</h3>";
                        } elseif ($t == "l") {
                            print "<h3>Cen�rios e termos do l�xico que referenciam este termo</h3>";
                        } elseif ($t == "oc") {
                            print "<h3>Rela��es do conceito</h3>";
                        } elseif ($t == "or") {
                            print "<h3>Conceitos referentes � rela��o</h3>";
                        } elseif ($t == "oa") {
                            print "<h3>Axioma</h3>";
                        }
                        ?>   





            <!--                     PART FOUR                            --> 


    <?php
    frame_inferior($c, $t, $id);
  // SCRIPT CHAMADO PELO HEADING.PHP 
} elseif (isset($project_id)) {         
    //Was passed a variable $ id_projeto. This variable should contain the id of a
    // Project that the User is registered. However, as the passage eh
    // Done using JavaScript (in heading.php), we check if this id really
    // Corresponds to a project that the User has access (security). 
    check_proj_perm($_SESSION['id_usuario_corrente'], $project_id) or die("Permissao negada");

    // Set a session variable corresponding to the current project 
    $_SESSION['id_projeto_corrente'] = $project_id;
    ?>    

            <table ALIGN=CENTER> 
                <tr> 
                    <th>Projeto:</th> 
                    <td CLASS="Estilo"><?= simple_query("nome", "projeto", "id_projeto = $project_id") ?></td> 
                </tr> 
                <tr> 
                    <th>Data de cria��o:</th> 
                <?php
                $date = simple_query("data_criacao", "projeto", "id_projeto = $project_id");
                ?>    

                    <td CLASS="Estilo"><?= formataData($date) ?></td> 

                </tr> 
                <tr> 
                    <th>Descri��o:</th> 
                    <td CLASS="Estilo"><?= nl2br(simple_query("descricao", "projeto", "id_projeto = $project_id")) ?></td> 
                </tr> 
            </table> 

    <?php
// Scenario - Choosing Project
// Purpose: Allow Administrator / Usurio choose a design.
// Context: The Administrator / Usurio want to choose a design.
// Pre-conditions: Login Become Administrator
// Actors: Administrator Usurio
// Resources: registered users
// Episdios: If the User select from the list of projects a project of which he is
// Administrator, see Administrator chooses Project.
// Otherwise, see Usurio choose Project.
// Check if the User Administrator eh this project
    if (is_admin($_SESSION['id_usuario_corrente'], $project_id)) {
        ?>    

                <br> 
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Voc� � um administrador deste projeto:</th> 

                    <?php
// Scenario - Project Administrator chooses
// Purpose: Allow the administrator to choose a project.
// Context: The administrator wants to choose a design.
// Pre-conditions: Login Become administrator selected project.
// Actors: Administrator
// Resources: Project doAdministrador
// Episdios: The administrator selects the list of projects a project of which he is
// Administrator.
// Appearing on the screen of the options:
// Check-applications for alteration of scenario (see Check requests for alteration
// The scenario);
// - Check requests for alteration of terms of the lexicon
// - (Check to see requests for alteration of terms of the lexicon);
// - Add-usurio (at present) in this project (see Add Usurio);
// - J-Relate existing users with this design
// - (See Relate users with projects);
// - Generate-xml this project (see Generate XML reports); 
                    ?>    
                    </TR>

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="addUsuario();">Adicionar usu�rio (n�o cadastrado) neste projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="relUsuario();">Adicionar usu�rios j� existentes neste projeto</a></td> 
                    </TR>   

                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="pedidoCenario();">Verificar pedidos de altera��o de Cen�rios</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="pedidoLexico();">Verificar pedidos de altera��o de termos do L�xico</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="pedidoConceito();">Verificar pedidos de altera��o de Conceitos</a></td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="pedidoRelacao();">Verificar pedidos de altera��o de Rela��es</a></td> 
                    </TR>


                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
                    </TR>       
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="recuperaXML();">Recuperar XML deste projeto</a></td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR> 

                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="geraOntologia();">Gerar ontologia deste projeto</a></td> 
                    </TR>            
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="geraDAML();">Gerar DAML da ontologia do projeto</a></td> 
                    </TR> 
                    <TR> 
                        <td CLASS="Estilo"><a href="#" onClick="recuperaDAML();">Hist�rico em DAML da ontologia do projeto</a></td> 
                    </TR>           
                    <TR> 
                        <td CLASS="Estilo"><a href="http://www.daml.org/validator/" target="new">*Validador de Ontologias na Web</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><a href="http://www.daml.org/2001/03/dumpont/" target="new">*Visualizador de Ontologias na Web</a></td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo">&nbsp;</td> 
                    </TR>
                    <TR> 
                        <td CLASS="Estilo"><font size="1">*Para usar Ontologias Geradas pelo C&L: </font></td>               
                    </TR>
                    <TR> 
                        <td CLASS="Estilo">   <font size="1">Hist�rico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>             
                    </TR>
                </table>


                <?php
            } else {
                ?>	
                <br>
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Voc� n�o � um administrador deste projeto:</th> 	
                    </tr>	
                    <tr> 
                        <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
                    </tr>  
                </table>			
                <?php
            }
        } else {        // SCRIPT CALLED BY index.php 
            ?>    

            <p>Select a project above, or create a new project.</p> 

                    <?php
                }
                ?>    
        <i><a href="showSource.php?file=main.php">See the source!</a></i> 
    </body> 

</html> 

