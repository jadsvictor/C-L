<?php

// Function that loads the vector with all the titles of the lexicons and their synonyms least the title of the
//Lexicon passed in the variable $ id_lexico_atual and their synonyms
//Function that loads vector with all titles and synonyms in the lexicons of less id id_lexico_atual

function carrega_vetor_lexicos($id_projeto, $id_lexico_atual, $semAtual) {
    
    //testes if the variable is not null
    assert($id_projeto != NULL);
    assert($id_lexico_atual != NULL);
    assert($semAtual != NULL);
    
    //tests if the variable has the correct type
    assert(is_string($id_projeto));
    assert(is_string($id_lexico_atual));
    assert(is_string($semAtual));
    
    $vetorDeLexicos = array();
    if ($semAtual) {
        $queryLexicos = "SELECT id_lexico, nome    
			FROM lexico    
			WHERE id_projeto = '$id_projeto' 
                        AND id_lexico <> '$id_lexico_atual' 
			ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome 
			FROM sinonimo
			WHERE id_projeto = '$id_projeto' 
                        AND id_lexico <> '$id_lexico_atual' 
			ORDER BY nome DESC";
    } else {

        $queryLexicos = "SELECT id_lexico, nome    
			FROM lexico    
			WHERE id_projeto = '$id_projeto' 
			ORDER BY nome DESC";

        $querySinonimos = "SELECT id_lexico, nome    
			FROM sinonimo
			WHERE id_projeto = '$id_projeto' 
                        ORDER BY nome DESC";
    }

    $resultadoQueryLexicos = mysql_query($queryLexicos) or
            die("Erro ao enviar a query de selecao na tabela lexicos !" . \mysql_error());

    $i = 0;
    while ($linhaLexico = mysql_fetch_object($resultadoQueryLexicos)) {
        $vetorDeLexicos[$i] = $linhaLexico;
        $i++;
    }

    $resultadoQuerySinonimos = mysql_query($querySinonimos) or
            die("Erro ao enviar a query de selecao na tabela sinonimos !" . mysql_error());
    while ($linhaSinonimo = mysql_fetch_object($resultadoQuerySinonimos)) {
        $vetorDeLexicos[$i] = $linhaSinonimo;
        $i++;
    }
    return $vetorDeLexicos;
}

//$ Id = id_cenario_atual the current scenario, so it does not create a link to itself
//Function that loads the vector with all titles of scenarios under the title of the scenario
//Passed in the variable $ id_cenario_atual

function carrega_vetor_cenario($id_projeto, $id_cenario_atual, $semAtual) {
    
    //tests if the variable is not null
    assert($id_projeto != NULL);
    assert($id_cenario_atual_cenario_atual != NULL);
    assert($semAtual != NULL);
    
    //tests if the variable has the correct type
    assert(is_string($id_projeto));
    assert(is_string($id_cenario_atual));
    assert(is_string($semAtual));

    $vetorDeCenarios = 0;
    if (!isset($vetorDeCenarios)) {
        $vetorDeCenarios = array();
    }
    if ($semAtual) {
        $queryCenarios = "SELECT id_cenario, titulo    
			FROM cenario    
			WHERE id_projeto = '$id_projeto' 
                        AND id_cenario <> '$id_cenario_atual' 
			ORDER BY titulo DESC";
    } else {
        $queryCenarios = "SELECT id_cenario, titulo    
			FROM cenario    
			WHERE id_projeto = '$id_projeto' 
			ORDER BY titulo DESC";
    }

    $resultadoQueryCenarios = mysql_query($queryCenarios) or
            die("Erro ao enviar a query de selecao !!" . mysql_error());

    $i = 0;
    while ($linhaCenario = mysql_fetch_object($resultadoQueryCenarios)) {
        $vetorDeCenarios[$i] = $linhaCenario;
        $i++;
    }

    return $vetorDeCenarios;
}

function divide_array(&$vet, $ini, $fim, $tipo) {
    assert($vet =! Null);
    assert($ini =! Null);
    assert($fim =! Null);
    assert($tipo =! Null);
    
    $i = $ini;
    $j = $fim;
    $dir = 1;

    while ($i < $j) {
        if (strcasecmp($tipo, 'cenario') == 0) {
            if (strlen($vet[$i]->titulo) < strlen($vet[$j]->titulo)) {
                $str_temp = $vet[$i];
                $vet[$i] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        } else {
            if (strlen($vet[$i]->nome) < strlen($vet[$j]->nome)) {
                $str_temp = $vet[$i];
                $vet[$i] = $vet[$j];
                $vet[$j] = $str_temp;
                $dir--;
            }
        }
        if ($dir == 1)
            $j--;
        else
            $i++;
    }

    return $i;
}

// Sort the vector

function quicksort(&$vet, $ini, $fim, $tipo) {
    assert($vet =! Null);
    assert($ini =! Null);
    assert($fim =! Null);
    assert($tipo =! Null);
    
    if ($ini < $fim) {
        $k = divide_array($vet, $ini, $fim, $tipo);
        quicksort($vet, $ini, $k - 1, $tipo);
        quicksort($vet, $k + 1, $fim, $tipo);
    }
}

//Feature that builds the links according to the text, passed through the parameter $ text, lexicons, past
//Using parameter $ vetorDeLexicos and scenarios, passed through the parameter $ vetorDeCenarios  

function monta_links($texto, $vetorDeLexicos, $vetorDeCenarios) {
    assert($vetorDeCenarios =! Null);
    assert($vetorDeLexicos =! NULL);
    assert($texto =! Null);
    
    assert(is_string($texto));
    
    $copiaTexto = $texto;
    $vetorAuxLexicos = 0;
    $vetorAuxCenarios = 0;
    if (!isset($vetorAuxLexicos)) {
        $vetorAuxLexicos = array();
    }
    if (!isset($vetorAuxCenarios)) {
        $vetorAuxCenarios = array();
    }
    if (!isset($vetorDeCenarios)) {
        $vetorDeCenarios = array();
    }
    if (!isset($vetorDeLexicos)) {
        $vetorDeLexicos = array();
    }

    // If the vector is empty scenario it will only look for references to lexical


    if (count($vetorDeCenarios) == 0) {

        $i = 0;
        $a = 0;
        while ($i < count($vetorDeLexicos)) {
            $nomeLexico = escapes_metacharacters($vetorDeLexicos[$i]->nome);
            $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
            if (preg_match($regex, $copiaTexto) != 0) {
                $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                $vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
                $a++;
            }
            $i++;
        }
    } else {

        //If the vector of scenarios is not empty it will look for lexical and scenarios 

        $tamLexicos = count($vetorDeLexicos);
        $tamCenarios = count($vetorDeCenarios);
        $tamanhoTotal = $tamLexicos + $tamCenarios;
        $i = 0;
        $j = 0;
        $a = 0;
        $b = 0;
        $contador = 0;
        while ($contador < $tamanhoTotal) {
            if (($i < $tamLexicos ) && ($j < $tamCenarios)) {
                if (strlen($vetorDeCenarios[$j]->titulo) < strlen($vetorDeLexicos[$i]->nome)) {
                    $nomeLexico = escapes_metacharacters($vetorDeLexicos[$i]->nome);
                    $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
                        $a++;
                    }
                    $i++;
                } else {

                    $tituloCenario = escapes_metacharacters($vetorDeCenarios[$j]->titulo);
                    $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                    if (preg_match($regex, $copiaTexto) != 0) {
                        $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                        $vetorAuxCenarios[$b] = $vetorDeCenarios[$j];
                        $b++;
                    }
                    $j++;
                }
            } else if ($tamLexicos == $i) {

                $tituloCenario = escapes_metacharacters($vetorDeCenarios[$j]->titulo);
                $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxCenarios[$b] = $vetorDeCenarios[$j];
                    $b++;
                }
                else
                    $j++;
            } else if ($tamCenarios == $j) {

                $nomeLexico = escapes_metacharacters($vetorDeLexicos[$i]->nome);
                $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
                if (preg_match($regex, $copiaTexto) != 0) {
                    $copiaTexto = preg_replace($regex, " ", $copiaTexto);
                    $vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
                    $a++;
                }
                else
                    $i++;
            }
            else
                $contador++;
        }
    }

    // Add links to lexicons in text

    $indice = 0;
    $vetorAux = array();
    while ($indice < count($vetorAuxLexicos)) {
        $nomeLexico = escapes_metacharacters($vetorAuxLexicos[$indice]->nome);
        $regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
        $link = "<a title=\"L�xico\" href=\"main.php?t=l&id=" .
                $vetorAuxLexicos[$indice]->id_lexico . "\">" .
                $vetorAuxLexicos[$indice]->nome . "</a>";
        $vetorAux[$indice] = $link;
        $texto = preg_replace($regex, "$1wzzxkkxy" . $indice . "$3", $texto);
        $indice++;
    }
    $indice2 = 0;

    while ($indice2 < count($vetorAux)) {
        $linkLexico = ( $vetorAux[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxy" . $indice2 . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1" . $linkLexico . "$3", $texto);
        $indice2++;
    }


    // Adds links to scenarios in the text 

    $vetorAuxCen = array();
    while ($indice < count($vetorAuxCenarios)) {
        $tituloCenario = escapes_metacharacters($vetorAuxCenarios[$indice]->titulo);
        $regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
        $link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=" .
                $vetorAuxCenarios[$indice]->id_cenario . "\"><span style=\"font-variant: small-caps\">" .
                $vetorAuxCenarios[$indice]->titulo . "</span></a>$3";
        $vetorAuxCen[$indice] = $link;
        $texto = preg_replace($regex, "$1wzzxkkxyy" . $indice . "$3", $texto);
        $indice++;
    }

    while ($indice2 < count($vetorAuxCen)) {
        $linkCenario = ( $vetorAuxCen[$indice2] );
        $regex = "/(\s|\b)(wzzxkkxyy" . $indice2 . ")(\s|\b)/i";
        $texto = preg_replace($regex, "$1" . $linkCenario . "$3", $texto);
        $indice2++;
    }

    return $texto;
}

?>