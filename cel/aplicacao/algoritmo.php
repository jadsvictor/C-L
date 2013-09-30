<?php
include 'estruturas.php';
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
        <?php

        function verifica_consistencia() {
            return TRUE;
        }

        function compara_arrays($array1, $array2) {

            if (count($array1) != count($array2)) {
                return FALSE;
            }
            
            else{
            	//nothing to do
            }

            foreach ($array1 as $key => $elem) {
                if ($elem->verbo != $array2[$key]->verbo) {
                    return FALSE;
                }
                
                else{
                	//nothing to do
                }
            }
            return TRUE;
        }

/*
Scenary: assemble hierarchy.
Objective: Assemble concept hierarchy.
Context: Organization ontology in progress.
actors:
Features: System concept subconcepts list, list of concepts.
episodes:
- For each subconceito
* Browse the list of your key concepts.
* Add the key as a subconceito concept.
*/
        function montar_hierarquia($conc, $nova_lista, $list) {
            foreach ($nova_lista as $subcon) {
                $key = existe_conceito($subcon, $list);
                $conc->subconceitos[] = $subcon;
            }
        }
        

/*
Scenario: Translate the terms of lexical classified as subject and object.
Objective: To translate the terms of lexical classified as subject and object.
Context: Algorithm translation started.
Actors: User.
Features: System, list of subjects and objects, concepts list, list of relations.
episodes:
- For each element of the list of subjects and objects
* Create new concept with the same name and description like the notion of the element.
* For each element of the impact
. Check with the User the existence of the impact on the list of relations.
. If not, this impact include the list of relations.
. Include this list in relation to the concept relations.
. discover
* Include the concept in the list of concepts.
* Check consistency.
*/
        function traduz_sujeito_objeto($lista_de_sujeito_e_objeto, $conceitos, $relacoes, $axiomas) {
            for (; $_SESSION["index1"] < count($lista_de_sujeito_e_objeto); ++$_SESSION["index1"]) {
                $suj = $lista_de_sujeito_e_objeto[$_SESSION["index1"]];
                if (!isset($_SESSION["conceito"])) {
                    $_SESSION["salvar"] = "TRUE";
                    $_SESSION["conceito"] = new conceito($suj->nome, $suj->nocao);
                    $_SESSION["conceito"]->namespace = "proprio";
                } 
                
                else {
                    $_SESSION["salvar"] = "FALSE";
                }
                
                for (; $_SESSION["index2"] < count($suj->impacto); ++$_SESSION["index2"]) {
                    $imp = $suj->impacto[$_SESSION["index2"]];
                    if (trim($imp) == ""){
                        continue;
                    }
                    
                    else{
                    	//nothing to do
                    }
                    
                    if (!isset($_SESSION["verbos_selecionados"])){
                        $_SESSION["verbos_selecionados"] = array();
                    }
                    
                    else{
                    	//nothing to do
                    }
                    
                    if (!isset($_SESSION["impact"])) {
                        $_SESSION["impact"] = array();
                        $_SESSION["finish_insert"] = FALSE;
                    }
                    
                    else{
                    	//nothing to do
                    }
                    
                    while (!$_SESSION["finish_insert"]) {
                        if (!isset($_SESSION["exist"])) {
                            asort($relacoes);
                            $_SESSION["lista"] = $relacoes;
                            $_SESSION["nome1"] = $imp;
                            $_SESSION["nome2"] = $suj;
                            $_SESSION["job"] = "exist";
                            ?>
                            <SCRIPT language='javascript'>
                                document.location = "auxiliar_interface.php";
                            </SCRIPT>

                            <?php
                            exit();
                        }
                        
                         else{
                    	//nothing to do
                        }
                        
                        if ($_POST["existe"] == "FALSE") {

                            $nome = strtolower($_POST["nome"]);
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && (array_search($nome, $_SESSION["verbos_selecionados"]) !== null)) {
                                continue;
                            }
                            
                             else{
                    	        //nothing to do
                            }
                            $_SESSION["verbos_selecionados"][] = $nome;
                            $i = array_search($nome, $relacoes);
                            
                            if ($i === false) {
                                $_SESSION["impact"][] = (array_push($relacoes, $nome) - 1);
                            } 
                            
                            else {
                                $_SESSION["impact"][] = $i;
                            }
                            
                        } 
                        
                        else if ($_POST["indice"] != -1) {
                            session_unregister("exist");
                            
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && array_search($relacoes[$_POST["indice"]], $_SESSION["verbos_selecionados"]) !== false) {
                                continue;
                            }
                            
                             else{
                    	      //nothing to do
                            }
                            
                            $_SESSION["verbos_selecionados"][] = $relacoes[$_POST["indice"]];
                            $_SESSION["impact"][] = $_POST["indice"];
                            
                        } 
                        
                        else {
                            $_SESSION["finish_insert"] = TRUE;
                        }
                    }
                    
                    if (!isset($_SESSION["ind"])) {
                        $_SESSION["ind"] = 0;
                    }
                     
                     else{
                    	//nothing to do
                    }
                    
                    $_SESSION["verbos_selecionados"] = array();
                    for (; $_SESSION["ind"] < count($_SESSION["impact"]); ++$_SESSION["ind"]) {
                        if (!isset($_SESSION["predicados_selecionados"])){
                            $_SESSION["predicados_selecionados"] = array();
                        }
                        
                         else{
                    	//nothing to do
                        }
                        
                        $indice = $_SESSION["impact"][$_SESSION["ind"]];
                        $_SESSION["finish_relation"] = FALSE;
                        while (!$_SESSION["finish_relation"]) {
                            if (!isset($_SESSION["insert_relation"])) {
                                asort($conceitos);
                                $_SESSION["lista"] = $conceitos;
                                $_SESSION["nome1"] = $relacoes[$indice];
                                $_SESSION["nome2"] = $suj->nome;
                                $_SESSION["nome3"] = $imp;
                                $_SESSION["job"] = "insert_relation";
                                ?>
                                <SCRIPT language='javascript'>
                                    document.location = "auxiliar_interface.php";
                                </SCRIPT>
                                <?php
                                exit();
                            } 
                            
                            else if (isset($_SESSION["nome2"])) {
                                session_unregister("nome2");
                                session_unregister("nome3");
                                session_unregister("insert_relation");
                                if ($_POST["existe"] == "FALSE") {
                                    $conc = strtolower($_POST["nome"]);
                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($conc, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    $_SESSION["predicados_selecionados"][] = $conc;

                                    if (existe_conceito($conc, $_SESSION['lista_de_conceitos']) == -1) {
                                        if (existe_conceito($conc, $lista_de_sujeito_e_objeto) == -1) {
                                            $nconc = new conceito($conc, "");
                                            $nconc->namespace = $_POST['namespace'];
                                            $_SESSION['lista_de_conceitos'][] = $nconc;
                                        }
                                    }
                                    $ind_rel = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($ind_rel != -1) {
                                        if (array_search($conc, $_SESSION["conceito"]->
                                                        relacoes[$ind_rel]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$ind_rel]->
                                                    predicados[] = $conc;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] =
                                                new relacao_entre_conceitos($conc, $_SESSION["nome1"]);
                                    }
                                } else if ($_POST["indice"] != "-1") {
                                    $conc = $conceitos[$_POST["indice"]]->nome;
                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($conc, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    $_SESSION["predicados_selecionados"][] = $conc;
                                    $ind_rel = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($ind_rel != -1) {
                                        if (array_search($conc, $_SESSION["conceito"]->
                                                        relacoes[$ind_rel]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$ind_rel]->
                                                    predicados[] = $conc;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] =
                                                new relacao_entre_conceitos($conc, $_SESSION["nome1"]);
                                    }
                                } else {
                                    $_SESSION["finish_relation"] = TRUE;
                                }
                            }
                        }
                        $_SESSION["predicados_selecionados"] = array();
                    }

                    session_unregister("exist");
                    session_unregister("impact");
                    session_unregister("ind");
                    session_unregister("insert_relation");
                    session_unregister("insert");
                    session_unregister("verbos_selecionados");
                    session_unregister("predicados_selecionados");
                }
                $finish_disjoint = FALSE;
                while (!$finish_disjoint) {
                    if (!isset($_SESSION["axiomas_selecionados"]))
                        $_SESSION["axiomas_selecionados"] = array();
                    if (!isset($_SESSION["disjoint"])) {
                        $_SESSION["lista"] = $conceitos;
                        $_SESSION["nome1"] = $_SESSION["conceito"]->nome;
                        $_SESSION["job"] = "disjoint";
                        ?>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    if ($_POST["existe"] == "TRUE") {
                        $axioma = $_SESSION["conceito"]->nome . " disjoint " . strtolower($_POST["nome"]);
                        if (array_search($axioma, $axiomas) === false) {
                            $axiomas[] = $axioma;
                            $_SESSION["axiomas_selecionados"][] = $axioma;
                        }
                        session_unregister("disjoint");
                    } else {
                        $finish_disjoint = TRUE;
                    }
                }
                $_SESSION["axiomas_selecionados"] = array();
                $conceitos[] = $_SESSION["conceito"];
                asort($conceitos);
                if (!verifica_consistencia()) {
                    exit();
                }
                session_unregister("insert");
                session_unregister("disjoint");
                session_unregister("exist");
                session_unregister("insert_relation");
                session_unregister("conceito");
                $_SESSION["index2"] = 0;
            }
            $_SESSION["index1"] = 0;
            session_unregister("finish_insert");
            session_unregister("finish_relation");
        }

/*
Scenario: Translate the terms of lexical classified as a verb.
Objective: To translate the terms of lexical classified as verb.
Context: Algorithm translation started.
Actors: User.
Features: System, verb list, list of relations.
episodes:
- For each element of the list of verb
* Check with the User the existence of the verb in the list of relations.
* If not, include this in the list of verb relations.
* Check consistency.
*/
        function traduz_verbos($verbos, $relacoes) {
            for (; $_SESSION["index3"] < count($verbos); ++$_SESSION["index3"]) {
                $verbo = $verbos[$_SESSION["index3"]];
                if (!isset($_SESSION["exist"])) {
                    $_SESSION["salvar"] = "TRUE";
                    asort($relacoes);
                    $_SESSION["lista"] = $relacoes;
                    $_SESSION["nome1"] = $verbo->nome;
                    $_SESSION["nome2"] = $verbo;
                    $_SESSION["job"] = "exist";
                    ?>
                    <SCRIPT language='javascript'>
                        document.location = "auxiliar_interface.php";
                    </SCRIPT>
                    <?php
                    exit();
                }
                if ($_POST["existe"] == "FALSE") {
                    $nome = strtolower($_POST["nome"]);
                    if (array_search($nome, $relacoes) === false)
                        array_push($relacoes, $nome);
                }
                if (!verifica_consistencia()) {
                    exit();
                }
                session_unregister("exist");
                session_unregister("insert");
            }
            $_SESSION["index3"] = 0;
        }

/*
Scenario: Translate the terms of lexical classified as a state.
Objective: To translate the terms of lexical classified as a state.
Context: Algorithm traducao started.
Actors: User.
Features: System status list, list of concepts, list of relations, list of axioms.
episodes:
- For each element of the list of state
* For each element of the impact
. discover
* Check if the element has central importance in the ontology.
* If you have, translate as if it were a subject / object.
* Otherwise, translate as if it were a verb.
* Check consistency.
*/
        function traduz_estados($estados, $conceitos, $relacoes, $axiomas) {
            for (; $_SESSION["index4"] < count($estados); ++$_SESSION["index4"]) {
                $estado = $estados[$_SESSION["index4"]];
                $aux = array($estado);
                if (!isset($_SESSION["main_subject"])) {
                    $_SESSION["nome1"] = $estado->nome;
                    $_SESSION["nome2"] = $estado;
                    $_SESSION["job"] = "main_subject";
                    ?>
                    <p>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    
                    if (!isset($_SESSION["translate"])) {
                        if ($_POST["main_subject"] == "TRUE") {
                            $_SESSION["translate"] = 1;
                            traduz_sujeito_objeto($aux, &$conceitos, &$relacoes, &$axiomas);
                        } 
                        
                        else {
                            $_SESSION["translate"] = 2;
                            traduz_verbos($aux, &$relacoes);
                        }
                        
                    }
                     
                    else if ($_SESSION["translate"] == 1) {
                        traduz_sujeito_objeto($aux, &$conceitos, &$relacoes);
                    }
                     
                    else if ($_SESSION["translate"] == 2) {
                        traduz_verbos($aux, &$relacoes);
                    }
                    if (!verifica_consistencia()) {
                        exit();
                    }
                    
                    else{
                    	//nothing to do
                    }
                    
                    session_unregister("main_subject");
                    session_unregister("translate");
                }
                $_SESSION["index4"] = 0;
            }

/*
Scenario: Organize ontology.
Objective: Organize ontology.
Context: Lists of concepts, relations and axioms ready.
Actors: User.
Features: System concepts list, list of relations, list of axioms.
episodes:
- It is a copy of the list of concepts.
- For each element x in the list of concepts
* It creates a new list containing the element x.
* For each subsequent element y
. Compares the relationships of the elements x and y.
. If they have the same relations, add the element ya new list that already contains x.
. Y is removed from the list of concepts.
X * is removed from the list of concepts.
* If the new list has more than two elements, ie, if x share the same
relations with another term
. Searches for an element in the list of concepts that knife refers to all elements
the new list.
. If there is such an element, set hierarchy.
. If there is not, find out.
* Check consistency.
- Restore the list of concepts.
*/

            function organizar_ontologia($conceitos, $relacoes, $axiomas) {
                $_SESSION["salvar"] = "TRUE";
                $finish_relation = FALSE;
                while (!$finish_relation) {
                    $indice = 0;
                    if (!isset($_SESSION["reference"])) {
                        $_SESSION["lista"] = $conceitos;
                        $_SESSION["job"] = "reference";
                        ?>
                        <a href="auxiliar_interface.php">auxiliar_interface</a>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    
                    else{
                    	//nothing to do
                    }
                    session_unregister("reference");
                    $achou = FALSE;
                    if (isset($_POST['pai'])) {
                        $pai_nome = $_POST['pai'];
                        $key2 = existe_conceito($pai_nome, $conceitos);
                        $filhos = array();
                        foreach ($conceitos as $key3 => $filho) {
                            $filho_nome = trim($filho->nome);
                            if (isset($_POST[$key3])) {
                                $filhos[] = $filho_nome;
                            }
                            
                            else{
                    	     //nothing to do
                           }
                        }
                        if (count($filhos) > 0) {
                            montar_hierarquia(&$conceitos[$key2], $filhos, $conceitos);
                            $achou = true;
                        }
                        
                        else{
                    	//nothing to do
                        }
                    } 
                    
                    else {
                        $finish_relation = true;
                    }
                    if (!$achou) {
                        
                    }
                    
                    else{
                    	//nothing to do
                    }                    
                }
                if (!verifica_consistencia()) {
                    exit();
                }
                
                else{
                    	//nothing to do
                }
            }
/*
Scenario: Translate to ontology lexicon.
Objective: Translate to Lexicon Ontology.
Context: There are lists of elements of the lexicon organized by type, and these elements
are consistent.
Actors: User.
Features: System elements of the lexicon lists organized by type, lists of elements
ontology.
episodes:
- Create list of empty concepts.
- Create empty list of relations.
- Create empty list of axioms.
- Translate the terms of lexical classified as subject and object.
- Translate the terms of lexical classified as a verb.
- Translate the terms of lexical classified as a state.
- Organize the ontology.
*/
            function traduz() {
                if (isset($_SESSION["lista_de_sujeito"]) && isset($_SESSION["lista_de_objeto"]) &&
                        isset($_SESSION["lista_de_verbo"]) && isset($_SESSION["lista_de_estado"]) &&
                        isset($_SESSION["lista_de_conceitos"]) && isset($_SESSION["lista_de_relacoes"]) &&
                        isset($_SESSION["lista_de_axiomas"])) {
                    $sujeitos = $_SESSION["lista_de_sujeito"];
                    $objetos = $_SESSION["lista_de_objeto"];
                    $verbos = $_SESSION["lista_de_verbo"];
                    $estados = $_SESSION["lista_de_estado"];
                } else {
                    echo "ERRO! <br>";
                    exit();
                }
                $lista_de_sujeito_e_objeto = array_merge($sujeitos, $objetos);
                sort($lista_de_sujeito_e_objeto);
                $_SESSION['lista_de_sujeito_e_objeto'] = $lista_de_sujeito_e_objeto;
                if ($_SESSION["funcao"] == "sujeito_objeto") {
                    traduz_sujeito_objeto($lista_de_sujeito_e_objeto, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "verbo";
                }
                
                else{
                    	//nothing to do
                }
                    
                if ($_SESSION["funcao"] == "verbo") {
                    traduz_verbos($verbos, &$_SESSION["lista_de_relacoes"]);
                    $_SESSION["funcao"] = "estado";
                }
                
                else{
                    	//nothing to do
                }
                
                if ($_SESSION["funcao"] == "estado") {
                    traduz_estados($estados, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "organiza";
                }
                
                else{
                    	//nothing to do
                }
                
                if ($_SESSION["funcao"] == "organiza") {
                    organizar_ontologia(&$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "fim";
                }
                
                else{
                    	//nothing to do
                }

                echo 'O processo de gera��o de Ontologias foi conclu�do com sucesso!<br>
	N�o esque�a de clicar em Salvar.';
                ?>
            <p>
            <form method="POST" action="auxiliar_bd.php">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR">
            </form>
        </p>
    <?php
}

traduz();
?>

</body>
</html>