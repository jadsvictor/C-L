<?php
include_once "estruturas.php";
include_once 'auxiliar_algoritmo.php';

session_start();
?>
<html>
    <head>
        <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
        <style>

        </style>
    </head>

    <body>
        <table height=100% width=100% border=0>
            <tr>

                <td width='200'>
                    <iframe src='arv_frameset.htm' height="100%" width='200'>
                    </iframe>
                </td>

                <td align='left' valign='top'>
                    <?php

/*
Scenario: Check with the User the existence of a name on a list.
Purpose: Check with the User the existence of a name on a list.
Context: translation algorithm started.
Actors: User.
Features: System name list.
episodes:
- Update true or false, if true put the index together, false if the index and ignored.
*/
                    function exist($name, $list) {
                        
                    //tests if the variable is not null
                    assert($name != NULL);
                    assert($list != NULL);
    
                    //tests if the variable has the correct type
                    assert(is_string($name));
                    assert(is_string($list));
                    
                        $indice = -1;
                        foreach ($list as $key => $palavra) {
                            if (strstr($name, $palavra)) {
                                $indice = $key;
                                break;
                            }
                            
                            else{
                                 //nothing to do
                            }
                            
                        }
                        ?>
                        <form method="POST" action="algoritmo.php" id="exist_form">

                            <?php
                            if (count($_SESSION["verbos_selecionados"]) != 0) {
                                echo "Propriedades j� cadastradas para esse impacto:<p>";
                                foreach ($_SESSION["verbos_selecionados"] as $verbo) {
                                    echo "   - " . $verbo . "<br>";
                                }
                            }
                            
                            else{
                                  //nothing to do
                            }
                            
                            ?>
                            <b>Impacto:</b> <code>"<?= $name ?>"</code><br><br>
                            <b>A propriedade j� est� cadastrada na lista abaixo?</b><br>
                            <SELECT id='indice' name='indice' size=10 width="300">
                                <?php
                                foreach ($list as $key => $termo) {
                                    ?>
                                    <OPTION value='<?= $key ?>' <?php if ($indice == $key) echo "selected" ?> >
                                        <?php echo $termo ?></OPTION>
                                    <?php
                                }
                                ?>
                                <OPTION value="-1"></OPTION>
                            </SELECT><br>
                            <input type="radio" onClick="seExiste('TRUE');" 
                                   value="TRUE" id="existe" name="existe" 
                                   size="20" <?php
                                   if ($indice !== -1)
                                       ;
                                   echo"checked"
                                   ?>> sim
                            <input type="radio" onClick="seExiste('FALSE');"
                                   value="FALSE" id="existe" name="existe" 
                                   size="20" <?php
                                   if ($indice === -1)
                                       ;
                                   echo"checked"
                                   ?> > n�o <BR>
                            <input type="text" value="<?php print strip_tags($name) ?>"
                                   id="nome" name="nome" size="20">
                            <input type="submit" value="Inserir Propriedade" 
                                   id="B1" name="B1" disabled><BR>
                            <INPUT type="button" value="Pr�ximo Passo >>" 
                                   name="B2" onClick="fim();">
                            </p>
                            <script language="JavaScript">
                                function seExiste(valor)
                                {
                                    var nome = document.all.nome;
                                    var indice = document.all.indice;
                                    if (valor === 'TRUE')
                                    {
                                        nome.disabled = true;
                                        indice.disabled = false;
                                    }
                                    else
                                    {
                                        nome.disabled = false;
                                        indice.disabled = true;
                                    }
                                }
    <?php
    if ($indice != -1)
        echo "seExiste('TRUE');";
    else
        echo "seExiste('FALSE');";
    ?>
                                function fim()
                                {
                                    if (<?= count($_SESSION["verbos_selecionados"]) ?> !== 0)
                                    {
                                        document.all.indice.disabled = false;
                                        document.all.indice.selectedIndex =<?= count($list) ?>;
                                        document.all.existe[0].checked = true;
                                        var form = document.getElementById("exist_form");
                                        form.submit();
                                    }
                                    else
                                        alert("� necessario ao menos um verbo para cada impacto.");

                                }

                            </script>
                            <SCRIPT language="JavaScript">
                                var form = document.getElementById("exist_form");
                                form.B1.disabled = false;
                            </SCRIPT>
                        </form>
                        <?php
                        $_SESSION["exist"] = 1;
                    }

/*
Scenario: Checking the importance of a term with the help of the User.
Objective: To evaluate the importance of a term with the help of the User.
Context: translation algorithm started.
Actors: User.
Features: System term.
episodes:
- Returns TRUE if the importance and central.
*/
                    function importancia_central($termo, $impactos) {
                        
                    //tests if the variable is not null
                    assert($termo != NULL);
                    assert($impactos != NULL);
    
                    //tests if the variable has the correct type
                    assert(is_string($termo));
                    assert(is_string($impactos));
                        
                        ?>
                        <h3>Termo: <?= $termo ?></h3><br>
                        <?php
                        print("Impactos:<br>");
                        foreach ($impactos as $impacto) {
                            if (trim($impacto) == ""){
                                continue;
                            }
                            
                            else{
                                //nothing to do
                            }
                            
                            print(" - $impacto <br>");
                        }
                        print("O termo $termo vai transformar-se em:<br>");
                        ?>
                        <form method="POST" action="algoritmo.php">
                            <input type="radio" value="TRUE" name="main_subject" 
                                   size="20" checked> Conceito
                            <input type="radio" value="FALSE" name="main_subject" 
                                   size="20"> Propriedade
                            <input type="submit" value="OK" name="B1" size="20">
                            </p>
                        </form>
                        <?php
                        $_SESSION["main_subject"] = 1;
                    }

/* --------  UNCERTAIN --------
Scenario: Check if a concept references another.
Objective: To determine if a concept references another.
Context: translation algorithm started.
Actors: User.
Resources: Conceito1, conceito2.
episodes:
Returns TRUE if references.
*/
                    function faz_referencia($conceitos, $subconceitos) {
                        
                    //tests if the variable is not null
                    assert($conceitos != NULL);
                    assert($subconceitos != NULL);
    
                    //tests if the variable has the correct type
                    assert(is_string($conceitos));
                    assert(is_string($subconceitos));
                    
                        ?>

                        <form method="POST" action="algoritmo.php" id="reference_form" 
                              name="reference_form">
                            <table border="1" cellspacing="1" width="100%" id="AutoNumber1">
                                <tr>
                                    <td width="100%" colspan="2"><?php print("Organizar Hierarquia <br>") ?></td>
                                </tr>
                                <tr>
                                    <td width="50%">
                                        <?php
                                        foreach ($conceitos as $conc) {
                                            $concsel = $conc; //trocar
                                            print("<INPUT type='radio' name='pai' id='pai' 
                                  value='$conc->nome' onFocus='Salvar()'> <b> $conc->nome </b> <br>\n");
                                            foreach ($conc->subconceitos as $subc) {
                                                print("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-$subc<br>");
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td width="50%" align="left" valign="top">
                                        <?php
                                        foreach ($conceitos as $key => $conc) {
                                            $sel = false;
                                            $existe = array_search($conc->nome, $concsel->subconceitos);
                                            if ($existe !== false)
                                                $sel = true;

                                            print("<INPUT type='checkbox' id='$key'
                                                   name='$key' value='$conc->nome' 
                                                  if($sel)checked> <b> $conc->nome </b> <br>\n");
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" value="Incluir Relacao" name="B1" size="20">
                            <input type="button" value="Finalizar Algoritmo" name="B2" onClick='Sair()'>
                        </FORM>

                        <script>
                                document.reference_form.pai[0].checked = true;
                                function Sair()
                                {
                                    var salvar = window.confirm("Voce deseja salvar \n\
                                                                 os subconceitos selecionados?");
                                    if (!salvar)
                                    {
                                        for (var i = 0; i < document.reference_form.pai.length; i++)
                                        {
                                            if (document.reference_form.pai[i].checked)
                                            {
                                                document.reference_form.pai[i].checked = false;
                                            }
                                        }
                                        var form = document.getElementById("reference_form");
                                        form.submit();
                                    }
                                }

                                function Salvar()
                                {

    <?php
    foreach ($conceitos as $key => $conc) {
        print("var check = document.getElementById('$key');\n");
        print("check.checked = false;\n");
    }
    ?>

                                }
                        </script>

                        <?php
                        $_SESSION["reference"] = 1;
                    }

					/* -------- -------- UNCERTAIN
					Scenario: Inserts type.
					Objective: Let bd consistent.
					Context:.
					Actors: User.
					Resources: list of untyped terms.
					episodes:
					*/
					
                    function insere_tipo($list) {
                        
                        //tests if the variable is not null
                    assert($list != NULL);
    
                    //tests if the variable has the correct type
                    assert(is_string($list));

                        $_SESSION["tipos"] = 2;
                        ?>
                        <form method="POST" action="auxiliar_bd.php">
                            <p>
                                <?php
                                foreach ($list as $key => $termo) {
                                    echo ("<br> $termo <br>");
                                    ?>

                                    Sujeito <input type="radio" value="sujeito" 
                                                   name="type<?php echo $key ?>" size="20" checked>
                                    Objeto <input type="radio" value="objeto" 
                                                  name="type<?php echo $key ?>" size="20">
                                    Verbo <input type="radio" value="verbo" 
                                                 name="type<?php echo $key ?>" size="20">
                                    Estado <input type="radio" value="estado" 
                                                  name="type<?php echo $key ?>" size="20">

                                    <?php
                                }
                                ?>
                                <input type="submit" value="Enviar" name="B1" size="20">
                            </p>
                        </form>
                        <?php
                        $_SESSION["tipos"] = 1;
                    }

                    function insere_relacao($rel, $conc, $imp, $list) {
                        
                    //tests if the variable is not null
                    assert($rel != NULL);
                    assert($conc != NULL);
                    assert($imp != NULL);
                    assert($list != NULL);
    
                    //tests if the variable has the correct type
                    assert(is_string($rel));
                    assert(is_string($conc));
                    assert(is_string($imp));
                    assert(is_string($list));
                    
                        $_SESSION["insert_relation"] = 1;
                        $indice = strpos($imp, $rel);
                        $tam = strlen($rel);
                        $pred = trim(strip_tags(substr($imp, $indice + $tam)));
                        $tam = strlen($pred);
                        
                        if ($pred{$tam - 1} == ".") {
                            $pred{$tam - 1} = "\0";
                        }
                        
                        else{
                           //nothing to do
                        }
                        
                        if (($pred{0} == "a" || $pred{0} == "o") && $pred{1} == ' ') {
                            $pred{0} = " ";
                            $pred = trim($pred);
                        }
                        
                        else{
                           //nothing to do
                        }
                        
                        $indice2 = -1;
                        foreach ($list as $key => $palavra) {
                            if (trim($palavra->nome) !== "") {
                                if (strstr($pred, $palavra->nome)) {
                                    if (array_search($palavra->nome, $_SESSION["predicados_selecionados"]) === false) {
                                        $indice2 = $key;
                                        break;
                                    }
                                    
                                    else{
                                       //nothing to do
                                    }
                                    
                                }
                         
                                 else{
                                   //nothing to do
                                 }
                            }
                           
                           else{
                           //nothing to do
                           }
                           
                        }
                        $indice3 = -1;
                        foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key => $palavra) {
                            if (strstr($pred, $palavra->nome) && ( array_search($palavra->nome, $_SESSION["predicados_selecionados"]) === false )) {
                                $indice3 = $key;
                                break;
                            }
                        }
                        ?>
                        <form method="POST" nome="rel_form" id="rel_form" action="algoritmo.php">
                            <?php
                            print "<H3>Conceito: $conc</H3>";
                            print "<H4>Propriedade:	 $rel </H4><BR>";
                            if (count($_SESSION["predicados_selecionados"]) != 0) {
                                echo "Predicados j� cadastrados para essa propriedade:<p>";
                                foreach ($_SESSION["predicados_selecionados"] as $verbo) {
                                    echo "- " . $verbo . "<br>";
                                }
                            }
                            
                            else{
                                 //nothing to do
                            }
                            
                            ?>
                            <B>Impacto:</B><CODE> <?= $imp ?></CODE><br>
                            <BR>
                            <b>O predicado da rela��o j� est� cadastrado na lista abaixo?</b><br>
                            <SELECT id='indice' name='indice' size=10 width="300">
                                <?php
                                foreach ($list as $key => $termo) {
                                    ?>
                                    <OPTION value='<?= $key ?>' <?php
                                    if ($indice2 === $key){
                                        echo "selected";
                                    }
                                     
                                    else{
                                         //nothing to do
                                    }
                                    
                                        ?> > <?php echo $termo->nome ?></OPTION>
                                            <?php
                                        }
                                        ?>
                                <OPTION value="-1"></OPTION>
                            </SELECT><br>

                            <input type="radio" onClick="seExiste('TRUE');" value="TRUE"
                                   id="existe" name="existe" size="20" <?php
                                   
                                   if ($indice2 !== -1){
                                       echo"checked";
                                   }
                                   
                                   else{
                                       //nothing to do
                                   }
                                   
                                       ?>> sim
                            <input type="radio" onClick="seExiste('FALSE');" value="FALSE"
                                   id="existe" name="existe" size="20" <?php if ($indice2 === -1) echo"checked" ?> > n�o <BR>

                            <DIV id=naoExiste>
                                <BR>
                                <b>Se n�o existe, ele pertence � lista de elementos 
                                    do nosso namespace(abaixo)?</b><br>
                                <SELECT onChange='seleciona(this[this.selectedIndex].text);
                                        ' size=10 width="300">
                                            <?php
                                            $selected = false;
                                            foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key => $termo) {
                                                ?>
                                        <OPTION value='<?= $termo->nome ?>' <?php
                                        if ($indice3 === $key) {
                                            echo "selected";
                                            $selected = $termo->nome;
                                        }
                                        
                                         else{
                                             //nothing to do
                                        }
                                        
                                        ?> ><?php echo $termo->nome ?></OPTION>
                                                <?php
                                            }
                                            ?>
                                </SELECT><br>

                                <table>
                                    <tr>
                                        <td>predicado:</td><td><input type="text"
                                                                      value="<?php print strip_tags($pred) ?>
                                                                      " id="nome" name="nome" size="20"></td>
                                    </tr>
                                    <tr>
                                        <td>namespace:</td><td><input type="text" 
                                                                      value="proprio" id="namespace" 
                                                                      name="namespace" size="20"></td>
                                    </tr>
                                </table>

                            </div>
                            <input type="submit" value="Inserir Predicado" id="B1" name="B1" size="20">
                            <INPUT type="button" value="Pr�ximo Passo >>" name="B2" onClick="fim();">
                            </p>
                            <script language='javascript'>
                                function seleciona(valor)
                                {
                                    document.all.nome.value = valor;
                                    document.all.namespace.value = 'proprio';
                                }

                                function fim()
                                {
                                    if (<?= count($_SESSION["predicados_selecionados"]) ?> !== 0)
                                    {
                                        document.all.indice.disabled = false;
                                        document.all.indice.selectedIndex =<?= count($list) ?>;
                                        document.all.existe[0].checked = true;
                                        var form = document.getElementById("rel_form");
                                        form.submit();
                                    }
                                    else
                                        alert('� necess�rio ao menos um predicado para cada verbo');
                                }

                                function seExiste(valor)
                                {
                                    var indice = document.all.indice;
                                    var naoExiste = document.all.naoExiste;
                                    if (valor === 'TRUE')
                                    {
                                        indice.disabled = false;
                                        naoExiste.style.display = 'none';
                                    }
                                    else
                                    {
                                        indice.disabled = true;
                                        naoExiste.style.display = 'block';
                                    }
                                }

    <?php
    if ($indice2 !== -1){
        echo "seExiste('TRUE');";
    }
    
    else{
        echo "seExiste('FALSE');";
    }
    
    if ($selected !== false){
        print("\n document.all.nome.value = '$selected';");
    }
                    }
    ?>
                            </script>
                        </form>
                        <?php
                        $_SESSION['insert_relation'] = 1;
                    

                    function disjuncao($nome, $list) {
                        
                    //tests if the variable is not null
                    assert($nome != NULL);
                    assert($list != NULL);
                    
                    //tests if the variable has the correct type
                    assert(is_string($nome));
                    assert(is_string($list));
                        
                        $_SESSION["disjoint"] = 1;
                        if (count($_SESSION["axiomas_selecionados"]) != 0) {
                            echo "Termos disjunos j� discriminados para esse conceito:<p>";
                            foreach ($_SESSION["axiomas_selecionados"] as $axioma) {
                                echo "- " . $axioma . "<br>";
                            }
                            echo "</p>";
                        }
                        
                        else{
                            //nothing to do
                        }
                        
                        print "Existe algum termo disjunto do conceito <b>$nome</b> na lista abaixo ou no vocabul�rio m�nimo?";
                        ?>
                        <form id='rel_form' name='rel_form'  method="POST" action="algoritmo.php">
                            <p>
                                <SELECT onChange='seleciona(this[this.selectedIndex].text);
                                        ' name="indice" size=10 width="300">
                                            <?php
                                            foreach ($_SESSION["lista_de_sujeito_e_objeto"] as $key => $termo) {
                                                if (strcmp($termo->nome, $nome) != 0) {
                                                    ?>
                                            <OPTION value='<?= $termo->nome ?>'>
                                                <?php echo $termo->nome ?></OPTION>
                                            <?php
                                        }
                                    }
                                    ?>
                                </SELECT><br>

                            <table>
                                <tr>
                                    <td>conceito:</td><td><input type="text" value="<?php print strip_tags($pred) ?>" id="nome" name="nome" size="20"></td>
                                </tr>
                                <tr>
                                    <td>namespace:</td><td><input type="text" value="proprio" id="namespace" name="namespace" size="20"></td>
                                </tr>
                            </table>
                            <input type="radio" onClick="seExiste('TRUE');" value="TRUE"
                                   name="existe" size="20"> sim
                            <input type="radio" onClick="seExiste('FALSE');" value="FALSE" 
                                   name="existe" size="20" checked> n�o<br>
                            <input type="button" value="Inserir Axioma" id="B1" 
                                   name="B1" onClick="insere();">
                            <INPUT type="button" value="Pr�ximo Passo >>" name="B2" onClick="fim();">
                            </p>
                            <script language='javascript'>
                                function seleciona(valor)
                                {
                                    document.all.nome.value = valor;
                                    document.all.namespace.value = 'proprio';
                                }

                                function insere()
                                {
                                    if (document.all.nome.value === '')
                                    {
                                        alert('Se existe, favor defina o conceito.');
                                    } else
                                    {
                                        var form = document.getElementById("rel_form");
                                        form.submit();
                                    }
                                }

                                function fim()
                                {
                                    document.all.existe[1].checked = true;
                                    var form = document.getElementById("rel_form");
                                    form.submit();
                                }

                                function seExiste(valor)
                                {
                                    var nome = document.all.nome;
                                    var indice = document.all.indice;
                                    var B1 = document.all.B1;
                                    if (valor === 'FALSE')
                                    {
                                        nome.disabled = true;
                                        indice.disabled = true;
                                        B1.disabled = true;
                                    }
                                    else
                                    {
                                        nome.disabled = false;
                                        indice.disabled = false;
                                        B1.disabled = false;
                                    }
                                }

                                seExiste('FALSE');
                            </script>
                        </form>
                        <?php
                    }

                    if (isset($_SESSION["job"])) {
                        if ($_SESSION["job"] == "exist") {
                            if ($_SESSION['funcao'] == 'sujeito_objeto') {
                                ?>
                                <h3>Conceito: <?= $_SESSION["nome2"]->nome ?></h3><br>
                                <?php
                            } 
                            
                            else if ($_SESSION['funcao'] == 'verbo') {
                                ?>
                                <h3>Verbo: <?= $_SESSION["nome2"]->nome ?></h3><br>
                                <?php
                            } 
                            
                            else if (isset($_SESSION["translate"])) {
                                if ($_SESSION["translate"] == 1) {
                                    ?>
                                    <h3>Conceito: <?= $_SESSION["nome2"]->nome ?></h3><br>
                                    <?php
                                } 
                                
                                else {
                                    ?>
                                    <h3>Verbo: <?= $_SESSION["nome2"]->nome ?></h3><br>
                                    <?php
                                }
                            }
                            exist($_SESSION["nome1"], &$_SESSION["lista"]);
                        }
                        
                        else if ($_SESSION["job"] == "insert") {
                            insert($_SESSION["nome1"], &$_SESSION["lista"]);
                        } 
                        
                        else if ($_SESSION["job"] == "main_subject") {
                            importancia_central($_SESSION["nome1"], $_SESSION['nome2']->impacto);
                        }
                        
                        else if ($_SESSION["job"] == "reference") {
                            //faz_referencia($_SESSION["lista"][0], $_SESSION["lista"][1]);
                            faz_referencia($_SESSION["lista"], $_SESSION["nome1"]);
                        } 
                        
                        else if ($_SESSION["job"] == "type") {
                            insere_tipo($_SESSION["lista"]);
                        } 
                        
                        else if (isset($_SESSION["nome2"]) && $_SESSION["job"] == "insert_relation") {
                            insere_relacao($_SESSION["nome1"], $_SESSION["nome2"], $_SESSION["nome3"], $_SESSION["lista"]);
                        } 
                        
                        else if ($_SESSION["job"] == "disjoint") {
                            disjuncao($_SESSION["nome1"], $_SESSION["lista"]);
                        }
                        ?>
                        <p>
                        <form method="POST" action="auxiliar_bd.php" id="salvar_form">
                            <input type="hidden" value="TRUE" name="save" size="20" >
                            <input type="submit" value="SALVAR" <?php
                    if ($_SESSION["salvar"] == "FALSE")
                        echo "disabled"
                            ?> >
                        </form>
                        </p>

                        <?php
                    }
                    else {
                        ?>
                        <form method="POST" action="algoritmo_inicio.php">
                            <p>
                                <font size="4">Carregar Ontologia?</font></p>
                            <p>
                                <input type="radio" value="TRUE" name="load" size="20"> sim
                                <input type="radio" value="FALSE" name="load" size="20"> n�o
                                <input type="submit" value="Enviar" name="B1" size="20">
                            </p>
                        </form>
                        <?php
                    }
                    ?>

                    <p><a href="inicio.php">Reiniciar Ontologia</a></p>
                </td>
            </tr>
        </table>
    </body>
</html>


