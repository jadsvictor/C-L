<?php

// Alt_lexico.php: This script makes a request for alteration of a project lexicon.
// The User receives a form with the current lexicon (ie with completed fields)
// And may make changes in all fields but name. At the end of the main screen
// Returns to the start screen and the tree and closed. The form of alteration and tb closed.
// File Caller: main.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");
include("functionsBD/simple_query.php");

chkUser("index.php");

$database_conection = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {     
	 
    if (!isset($listSinonimo)){
        $listSinonimo = array();
    }
    
    else{
       	//nothing to do
    }
        
// strip the sinonimos case has a null.
    $count = count($listSinonimo);
    for ($i = 0; $i < $count; $i++) {
    	
        if ($listSinonimo[$i] == "") {
            $listSinonimo = null;
        }
        
        else{
           	//nothing to do
        }
    }
    
    foreach ($listSinonimo as $key => $sinonimo) {
        $listSinonimo[$key] = str_replace(">", " ", str_replace("<", " ", $sinonimo));
    }
    inserirPedidoAlterarLexico($project_id, $id_lexico, $nome, $nocao, $impacto, 
                               $justificativa, $_SESSION['id_usuario_corrente'], 
                               $listSinonimo, $classificacao);
    ?>
    <html>
        <head>
            <title>Alterar L�xico</title>
        </head>
        <body>
            <script language="javascript1.3">

                opener.parent.frames['code'].location.reload();
                opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=
                                                              $_SESSION['id_projeto_corrente'] ?>');

            </script>

            <h4>Opera��o efetuada com sucesso!</h4>

            <script language="javascript1.3">

                self.close();

            </script>

    <?php
} 

else { 
    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $selection = "SELECT * FROM lexico WHERE id_lexico = $id_lexico";
    $qrr = mysql_query($selection) or die("Erro ao executar a query");
    $result = mysql_fetch_array($qrr);
    $qSin = "SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico";
    $qrrSin = mysql_query($qSin) or die("Erro ao executar a query");
    
    ?>
        <html>
            <head>
                <title>Alterar L�xico</title>
            </head>
            <body>
                <script language="JavaScript">
                    <!--
                function TestarBranco(form)
                    {
                        nocao = form.nocao.value;

                        if (nocao === "")
                        {
                            alert(" Por favor, forne�a a NO��O do l�xico.\n \n\
                                    O campo NO��O � de preenchimento obrigat�rio.");
                            form.nocao.focus();
                            return false;
                        }
                    }
                    function addSinonimo()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];
                        if (document.forms[0].sinonimo.value === ""){
                            return;
                            }
                        listSinonimo.options[listSinonimo.length] = 
                                new Option(document.forms[0].sinonimo.value, 
                                document.forms[0].sinonimo.value);
                        document.forms[0].sinonimo.value = "";
                        document.forms[0].sinonimo.focus();
                    }

                    function delSinonimo()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];
                        if (listSinonimo.selectedIndex === -1)
                            return;
                        else
                            listSinonimo.options[listSinonimo.selectedIndex] = null;
                        delSinonimo();
                    }

                    function doSubmit()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        for (var i = 0; i < listSinonimo.length; i++)
                            listSinonimo.options[i].selected = true;
                        return true;
                    }

    <?php
    
// Scenarios - Change Lexicon

// Purpose: Allow changing an input by a user lexicon
// Context: User want to change a lexicon previously registered
// Precondition: Login lexicon, registered in the system
// Actors: User
// Resources: System, data registered
// Episodes: The system will provide to the user the same screen INCLUDE LEXICON,
// But with the following data from the lexical to be changed filled
// And editable in their respective fields: Concept and Impact.
// Design and Name fields will be filled, but not editable.
// Will display a field Rationale for the user to place one

    ?>

                </SCRIPT>

                <h4>Alterar S�mbolo</h4>
                <br>
                <form action="?id_projeto=<?= $project_id ?>" method="post" 
                      onSubmit="return(doSubmit());">
                    <table>
                        <input type="hidden" name="id_lexico" value="<?= $result['id_lexico'] ?>">

                        <tr>
                            <td>Projeto:</td>
                            <td><input disabled size="48" type="text" value="<?= $project_name ?>"></td>
                        </tr>
                        <tr>
                            <td>Nome:</td>
                            <td><input disabled maxlength="64" name="nome_visivel" 
                                       size="48" type="text" value="<?= $result['nome']; ?>">
                                <input type="hidden"  maxlength="64" name="nome" 
                                       size="48" type="text" value="<?= $result['nome']; ?>">
                            </td>
                        </tr>

                        <tr valign="top">
                            <td>Sin�nimos:</td>
                            <td width="0%">
                                <input name="sinonimo" size="15" type="text" maxlength="50">             
                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Adicionar" 
                                                               onclick="addSinonimo();">
                                &nbsp;&nbsp;<input type="button" value="Remover" 
                                                   onclick="delSinonimo();">&nbsp;
                            </td>
                        </tr>

                        <tr> 
                            <td>
                            </td>   
                            <td width="100%">
                        <left><select multiple name="listSinonimo[]"  style="width: 400px;"  size="5"><?php
    while ($rowSin = mysql_fetch_array($qrrSin)) {
        ?>
                                    <option value="<?= $rowSin["nome"] ?>"><?= $rowSin["nome"] ?></option>
        <?php
    }
    ?>
                                <select></left><br> 
                                    </td>
                                    </tr>

                                    <tr>
                                        <td>No��o:</td>
                                        <td>
                                            <textarea name="nocao" cols="48" 
                                                      rows="3" ><?= $result['nocao']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Impacto:</td>
                                        <td>
                                            <textarea name="impacto" cols="48" 
                                                      rows="3"><?= $result['impacto']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Classifica�ao:</td>
                                        <td>
                                            <SELECT id='classificacao' name='classificacao' 
                                                    size=1 width="300">
                                                <OPTION value='sujeito' <?php 
                                                    if ($result['tipo'] == 'sujeito')
                                                        echo "selected" ?>>Sujeito</OPTION>
                                                <OPTION value='objeto' <?php 
                                                    if ($result['tipo'] == 'objeto') 
                                                        echo "selected" ?>>Objeto</OPTION>
                                                <OPTION value='verbo' <?php 
                                                    if ($result['tipo'] == 'verbo') 
                                                        echo "selected" ?>>Verbo</OPTION>
                                                <OPTION value='estado' <?php 
                                                    if ($result['tipo'] == 'estado') 
                                                        echo "selected" ?>>Estado</OPTION>
                                            </SELECT>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                                        <td><textarea name="justificativa" cols="48" rows="6"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2" height="60">
                                            <input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Alterar S�mbolo">
                                        </td>
                                    </tr>
                                    </table>
                                    </form>
                                    <center><a href="javascript:self.close();">Fechar</a></center>
                                    <br><i><a href="showSource.php?file=alt_lexico.php">Veja o c�digo fonte!</a></i>
                                    </body>
                                    </html>

    <?php
}
?>
