<?php
session_start();

// Add_cenario.php: This script registers a new scenary design.
// Is passed, through the URL, a variable $ id_projeto that
// Indicates that the project should be inserted the new scenary.

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

chkUser("index.php");

if (!isset($sucesso)) {
    $sucesso = "n";
}


$connect_database = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    $ret = checarCenarioExistente($_SESSION['id_projeto_corrente'], $titulo);
    ?>  <!-- ADICIONEI ISTO PARA TESTES -->
    <!--
       RET = <?= $ret ?> => RET = <?PHP $ret ? print("TRUE")  : print("FALSE") ; ?><BR>
    $sucesso        = <?= $sucesso ?><BR>
    _GET["sucesso"] = <?= $_GET["sucesso"] ?><BR>   
    -->
    <?PHP
    if ($ret == true) {
        print("<!-- Tentando Inserir Cenario --><BR>");

        // Replaces all occurrences of ">" and "<" with "" 
        $titulo = str_replace(">", " ", str_replace("<", " ", $titulo));
        $objetivo = str_replace(">", " ", str_replace("<", " ", $objetivo));
        $contexto = str_replace(">", " ", str_replace("<", " ", $contexto));
        $atores = str_replace(">", " ", str_replace("<", " ", $atores));
        $recursos = str_replace(">", " ", str_replace("<", " ", $recursos));
        $excecao = str_replace(">", " ", str_replace("<", " ", $excecao));
        $episodios = str_replace(">", " ", str_replace("<", " ", $episodios));
        inserirPedidoAdicionarCenario($_SESSION['id_projeto_corrente'], $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $_SESSION['id_usuario_corrente']);
        print("<!-- Cenario Inserido Com Sucesso! --><BR>");
    } else {
        ?>
        <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">Este cen�rio j� existe!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body></html>
        <?php
        return;
    }
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
    <?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>
        add_cenario.php?id_projeto =<?= $project_id ?> & sucesso = s" ;
                location.href = "add_cenario.php?id_projeto=<?= $project_id ?>&sucesso=s";

    </script>

    <?php
} else {   
	 // Script called trough the top menu
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    ?>

    <html>
        <head>
            <title>Adicionar Cen�rio</title>
        </head>
        <body>
            <script language="JavaScript">
                <!--
                function TestarBranco(form)
                {
                    titulo = form.titulo.value;
                    objetivo = form.objetivo.value;
                    contexto = form.contexto.value;

                    if ((titulo === ""))
                    {
                        alert("Por favor, digite o titulo do cen�rio.");
                        form.titulo.focus();
                        return false;
                    } else {
                        padrao = /[\\\/\?"<>:|]/;
                        OK = padrao.exec(titulo);
                        if (OK)
                        {
                            window.alert("O t�tulo do cen�rio n�o pode conter nenhum \n\
                                          dos seguintes caracteres:   / \\ : ? \" < > |");
                            form.titulo.focus();
                            return false;
                        }
                    }

                    if ((objetivo === ""))
                    {
                        alert("Por favor, digite o objetivo do cen�rio.");
                        form.objetivo.focus();
                        return false;
                    }

                    if ((contexto === ""))
                    {
                        alert("Por favor, digite o contexto do cen�rio.");
                        form.contexto.focus();
                        return false;
                    }
                }
                //-->

    <?php
// Scenary - Include Scenary
// Purpose: Allow user to include a new scenario
// Context: User to include a new scenario.
// Precondition: Login, backdrop not registered
// Actors: User, System
// Resources: Data to be registered
// Episodes: The system provides the user a screen with the following text fields:
// - Name Scenary
// - Purpose. Restriction: Text box with at least 5 lines of writing visible
// - Context. Restriction: Text box with at least 5 lines of writing visible
// - Actors. Restriction: Text box with at least 5 lines of writing visible
// - Resources. Restriction: Text box with at least 5 lines of writing visible
// - Exception. Restriction: Text box with at least 5 lines of writing visible
// - Episodes. Restriction: Text box with at least 16 lines of writing visible
// - Button to confirm the inclusion of the new scenario
// Restrictions: After clicking the confirmation button,
// The system checks whether all fields have been filled.
// Exception: If all fields are empty, returns to the user a warning message
// All fields must be completed and a button to return to the previous page.

    ?>

            </SCRIPT>

            <h4>Adicionar Cen�rio</h4>
            <br>
    <?php
    if ($sucesso == "s") {
        ?>
                <p style="color: blue; font-weight: bold; text-align: center">Cen�rio inserido com sucesso!</p>
                <?php
            }
            ?>    
            <form action="" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="51" type="text" value="<?= $project_name ?>"></td>
                    </tr>
                    <td>T�tulo:</td>
                    <td><input size="51" name="titulo" type="text" value=""></td>                
                    <tr>
                        <td>Objetivo:</td>
                        <td><textarea cols="51" name="objetivo" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
                        <td><textarea cols="51" name="contexto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
                        <td><textarea cols="51" name="atores" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
                        <td><textarea cols="51" name="recursos" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce��o:</td>
                        <td><textarea cols="51" name="excecao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis�dios:</td>
                        <td><textarea cols="51" name="episodios" rows="5" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" 
                                                                          type="submit" onClick="return TestarBranco(this.form);" 
                                                                          value="Adicionar Cen�rio"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=add_cenario.php">Veja o c�digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
