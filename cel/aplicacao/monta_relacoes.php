<?php

include_once("monta_relacoes.php");
include_once("coloca_links.php");
### MONTA AS RELACOES USADAS NO MENU LATERAL###

function monta_relacoes($project_id) {
    // Apaga todas as rela��es existentes das tabelas centocen, centolex e lextolex

    $DATABASE = new PGDB ();
    $sql1 = new QUERY($DATABASE);
    $sql2 = new QUERY($DATABASE);
    $sql3 = new QUERY($DATABASE);

    //$sql1->execute ("DELETE FROM centocen");
    //$sql2->execute ("DELETE FROM centolex") ;
    //$sql3->execute ("DELETE FROM lextolex") ;
    // Refaz as rela��es das tabelas centocen, centolex e lextolex
    //seleciona todos os cenarios

    $q = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $project_id
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    $qrr = mysql_query($q) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($qrr)) { // Para todos os cenarios 
        $current_scenario_id = $result['id_cenario'];

        // Monta vetor com titulo dos cenarios

        $scenarios_vector = carrega_vetor_cenario($project_id, $current_scenario_id);

        // Monta vetor com nome e sinonimos de todos os lexicos

        $lexicons_vector = carrega_vetor_todos($project_id);

        // Ordena o vetor de lexico pela quantidade de palavaras do nome ou sinonimo

        quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');

        // Ordena o vetor de cenarios pela quantidade de palavras do titulo

        quicksort($scenarios_vector, 0, count($scenarios_vector) - 1, 'cenario');

        ## Titulo

        $title = $result['titulo'];
        $tempTitle = cenario_para_lexico($current_scenario_id, $title, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempTitle);

        ## Objetivo

        $goal = $result['objetivo'];
        $tempGoal = cenario_para_lexico($current_scenario_id, $goal, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempGoal);

        ## Contexto

        $context = $result['contexto'];
        $tempContext = cenario_para_lexico_cenario_para_cenario($current_scenario_id, $context, $lexicons_vector, $scenarios_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempContext);

        ## Atores 

        $actors = $result['atores'];
        $tempActors = cenario_para_lexico($current_scenario_id, $actors, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempActors);

        ## Recursos 

        $means = $result['recursos'];
        $tempMeans = cenario_para_lexico($current_scenario_id, $means, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempMeans);

        ## Excecao

        $exception = $result['excecao'];
        $tempException = cenario_para_lexico($current_scenario_id, $exception, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempException);

        ## Episodios

        $episodes = $result['episodios'];
        $tempEpisodes = cenario_para_lexico_cenario_para_cenario($current_scenario_id, $episodes, $lexicons_vector, $scenarios_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempEpisodes);
    }

    // Seleciona todos os l�xicos

    $q = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $project_id
	          ORDER BY CHAR_LENGTH(nome) DESC";
    $qrr = mysql_query($q) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($qrr)) { // Para todos os lexicos
        $current_lexicon_id = $result['id_lexico'];

        // Monta vetor com nomes e sinonimos de todos os lexicos menos o lexico atual

        $lexicons_vector = carrega_vetor($project_id, $current_lexicon_id);

        // Ordena o vetor de lexicos pela quantidade de palavaras do nome ou sinonimo
        quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');

        ## Nocao

        $notion = $result['nocao'];
        $tempNotion = lexico_para_lexico($id_lexico, $notion, $lexicons_vector);
        adiciona_relacionamento($current_lexicon_id, 'lexico', $tempNotion);

        ## Impacto	

        $impact = $result['impacto'];
        $tempImpact = lexico_para_lexico($id_lexico, $impact, $lexicons_vector);
        adiciona_relacionamento($current_lexicon_id, 'lexico', $tempImpact);
    }
}

// marca as rela��es de l�xicos para l�xicos

function lexico_para_lexico($id_lexico, $text, $vetor_lexicos) {
    $number = 0;
    while ($number < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$number]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vetor_lexicos[$number]->id_lexico . "**$2" . "}$3", $text);
        $number++;
        // insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO lextolex (id_lexico_from, id_lexico_to)
        //		VALUES ($id_lexico, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $text;
}

// Marca as rela��es de cen�rios para l�xicos

function cenario_para_lexico($id_cenario, $text, $vetor_lexicos) {
    $number = 0;
    while ($number < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$number]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vetor_lexicos[$j]->id_lexico . "**$2" . "}$3", $text);
        $number++;
        // insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $text;
}

// Marca as rela��es de cen�rios para cen�rios

function cenario_para_cenario($id_cenario, $text, $vetor_cenarios) {
    $number = 0;
    while ($number < count($vetor_cenarios)) {
        $regex = "/(\s|\b)(" . $vetor_cenarios[$number]->titulo . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $text);
        $number++;
        // insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $text;
}

// Marca as rela�oes de cen�rio para cen�rio e cen�rio para l�xico no mesmo texto

function cenario_para_lexico_cenario_para_cenario($scenario_id, $text, $lexicons_vector, $scenarios_vector) {
    $i = 0;
    $j = 0;
    $k = 0;
    $total = count($lexicons_vector) + count($scenarios_vector);
    while ($k < $total) {
        if (strlen($scenarios_vector[$j]->titulo) < strlen($lexicons_vector[$i]->nome)) {
            $regex = "/(\s|\b)(" . $lexicons_vector[$i]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $lexicons_vector[$i]->id_lexico . "**$2" . "}$3", $text);
            $i++;

            // insere o relacionamento na tabela centolex
            //$q = "INSERT 
            //		INTO centolex (id_cenario, id_lexico)
            //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
            //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        } else {
            $regex = "/(\s|\b)(" . $scenarios_vector[$j]->titulo . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{c" . $scenarios_vector[$j]->id_cenario . "**$2" . "}$3", $text);
            $j++;
        }
        $k++;
    }
    return $text;
}

// Fun��o que adiciona os relacionamentos nas tabelas centocen, centolex e lextolex
// Atraves da analise das marcas
// id_from id do l�xico ou cen�rio que referencia outro cen�rio ou l�xico
// $tipo_from tipo de quem esta referenciando ( se � l�xico ou cen�rio)

function adiciona_relacionamento($id_from, $type_from, $text) {
    $i = 0; // indice do texto com marcadores
    $parser = 0; // verifica quando devem ser adicionadas as tags

    $new_text = "";
    while ($i < strlen(&$text)) {
        if ($text[$i] == "{") {
            $parser++;
            if ($parser == 1) { //adiciona link ao texto - abrindo
                $id_to = "";
                $i++;
                $type = $text[$i];
                $i++;
                while ($text[$i] != "*") {
                    $id_to .= $text[$i];
                    $i++;
                }
                if ($type == "l") {// Destino � um l�xico (id_lexico_to)
                    if (strcasecmp($type_from, 'lexico') == 0) {// Origem � um l�xico (id_lexico_from -> id_lexico_to)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l�xico para l�xico")</script>';
                        //adiciona rela��o de lexico para l�xico	
                    } else if (strcasecmp($type_from, 'cenario') == 0) {// Origem � um cen�rio (id_cenario -> id_lexico)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para l�xico")</script>';
                        //adiciona rela��o de cen�rio para l�xico
                    }
                }
                if ($type == "c") {// Destino � um cen�rio (id_cenario_to)
                    if (strcasecmp($type_from, 'cenario') == 0) {// Origem � um cenario (id_cenario_from -> id_cenario_to)
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para cen�rio")</script>';
                        // Relacionamentos do tipo cen�rio para cen�rio
                        // Adiciona relacao de cenario para cenario na tabela centocen
                        //$q = "INSERT 
                        //		INTO centocen (id_cenario_from, id_cenario_to)
                        //		VALUES ($id_from, " . $vetor_cenarios[$j]->id_cenario . ")";
                        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
                $i + 1;
            }
        } elseif ($text[$i] == "}") {
            $parser--;
        }
        $i++;
    }
}

?>