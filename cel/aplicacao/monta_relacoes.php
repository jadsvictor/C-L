<?php

include_once("monta_relacoes.php");
include_once("coloca_links.php");
// RIDING THE RELATIONS USED IN SIDE MENU

function monta_relacoes($project_id) {
	
	//test if the variable is not null
	assert ($project_id!= null);
	
    // Deletes all relations existing tables centocen, centolex and lextolex
    // Redo the relationships of the tables centocen, centolex and lextolex
    // selects all scenarios

    $select_scene = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $project_id
	          ORDER BY CHAR_LENGTH(titulo) DESC";
    $qrr_select_scene = mysql_query($select_scene) or die("Erro ao enviar a query");
    
    // For all scenarios
    while ($result = mysql_fetch_array($qrr_select_scene)) {  
        $current_scenario_id = $result['id_cenario'];

        // Ride vector title of scenarios

        $scenarios_vector = carrega_vetor_cenario($project_id, $current_scenario_id);

        // Ride vector with name and synonyms of all lexical

        $lexicons_vector = carrega_vetor_todos($project_id);

        // Sort the vector of the number of lexical words name or synonym

        quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');

        // Vector sorts of scenarios for the amount of words in title

        quicksort($scenarios_vector, 0, count($scenarios_vector) - 1, 'cenario');

        // Title

        $title = $result['titulo'];
        $tempTitle = cenario_para_lexico($current_scenario_id, $title, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempTitle);

        // Goal

        $goal = $result['objetivo'];
        $tempGoal = cenario_para_lexico($current_scenario_id, $goal, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempGoal);

        // Context

        $context = $result['contexto'];
        $tempContext = cenario_para_lexico_cenario_para_cenario($current_scenario_id, $context, $lexicons_vector, $scenarios_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempContext);

        // Actors 

        $actors = $result['atores'];
        $tempActors = cenario_para_lexico($current_scenario_id, $actors, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempActors);

        // Resources

        $means = $result['recursos'];
        $tempMeans = cenario_para_lexico($current_scenario_id, $means, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempMeans);

        // Exception

        $exception = $result['excecao'];
        $tempException = cenario_para_lexico($current_scenario_id, $exception, $lexicons_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempException);

        // Episodes

        $episodes = $result['episodios'];
        $tempEpisodes = cenario_para_lexico_cenario_para_cenario($current_scenario_id, $episodes, $lexicons_vector, $scenarios_vector);
        adiciona_relacionamento($current_scenario_id, 'cenario', $tempEpisodes);
    }

    // Select all the lexicons

    $select_lexicon = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $project_id
	          ORDER BY CHAR_LENGTH(nome) DESC";
    $qrr_select_lexicon = mysql_query($select_lexicon) or die("Erro ao enviar a query");

    while ($result = mysql_fetch_array($qrr_select_lexicon)) { 
        $current_lexicon_id = $result['id_lexico'];

        $lexicons_vector = carrega_vetor($project_id, $current_lexicon_id);

        quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');

        // Notion

        $notion = $result['nocao'];
        $tempNotion = lexico_para_lexico($id_lexico, $notion, $lexicons_vector);
        adiciona_relacionamento($current_lexicon_id, 'lexico', $tempNotion);

        // Impact	

        $impact = $result['impacto'];
        $tempImpact = lexico_para_lexico($id_lexico, $impact, $lexicons_vector);
        adiciona_relacionamento($current_lexicon_id, 'lexico', $tempImpact);
    }
}

// brand relationships from lexical to lexical

function lexico_para_lexico($id_lexico, $text, $vetor_lexicos) {
	
		//test if the variable is not null
		assert ($id_lexico!= null);
		assert ($text!= null);
		assert ($vetor_lexicos!= null);
	
    $number = 0;
    while ($number < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$number]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vetor_lexicos[$number]->id_lexico . "**$2" . "}$3", $text);
        $number++;
        // enter the relationship in the table centolex
        //$q = "INSERT 
        //		INTO lextolex (id_lexico_from, id_lexico_to)
        //		VALUES ($id_lexico, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $text;
}

// Brand relationships from scenarios to lexical

function cenario_para_lexico($id_cenario, $text, $vetor_lexicos) {
	
	//test if the variable is not null
	assert ($id_cenario!= null);
	assert ($text!= null);
	assert ($vetor_lexicos!= null);
	
    $number = 0;
    $j = 0;
    while ($number < count($vetor_lexicos)) {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$number]->nome . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{l" . $vetor_lexicos[$j]->id_lexico . "**$2" . "}$3", $text);
        $number++;
        
        }
    return $text;
}

// Brand relationships scenarios for scenarios

function cenario_para_cenario($id_cenario, $text, $vetor_cenarios) {
	
	//test if the variable is not null
	assert ($id_cenario!= null);
	assert ($text!= null);
	assert ($vetor_cenarios!= null);
	
    $number = 0;
    $j= 0;
    while ($number < count($vetor_cenarios)) {
        $regex = "/(\s|\b)(" . $vetor_cenarios[$number]->titulo . ")(\s|\b)/i";
        $text = preg_replace($regex, "$1{c" . $vetor_cenarios[$j]->id_cenario . "**$2" . "}$3", $text);
        $number++; 
        
        }
    return $text;
}

//Mark the relations from scenario to scenario and setting for lexicon in the same text

function cenario_para_lexico_cenario_para_cenario($scenario_id, $text, $lexicons_vector, $scenarios_vector) {
	
	//test if the variable is not null
	assert ($scenario_id!= null);
	assert ($text!= null);
	assert ($lexicons_vector!= null);
	assert ($scenarios_vector!= null);
	
    $i = 0;
    $j = 0;
    $k = 0;
    $total = count($lexicons_vector) + count($scenarios_vector);
    while ($k < $total) {
        if (strlen($scenarios_vector[$j]->titulo) < strlen($lexicons_vector[$i]->nome)) {
            $regex = "/(\s|\b)(" . $lexicons_vector[$i]->nome . ")(\s|\b)/i";
            $text = preg_replace($regex, "$1{l" . $lexicons_vector[$i]->id_lexico . "**$2" . "}$3", $text);
            $i++;

            // enter the relationship in the table centolex
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

// Function that adds tables centocen relationships, and centolex lextolex
// Through the analysis of brands
// Id id_from lexicon or scenario references another scenario or lexical
// $ Tipo_from whom this type of referencing (whether lexical or scenario)

function adiciona_relacionamento($id_from, $type_from, $text) {
	
	//test if the variable is not null
	assert ($id_from!= null);
	assert ($type_from!= null);
	assert ($text!= null);
	
    // index of bulleted text
    $index = 0; 
    // Check if the tags should be added
    $parser = 0; 

    while ($index < strlen(&$text)) {
        if ($text[$index] == "{") {
            $parser++;
            // add the text link - opening
            if ($parser == 1) { 
                $id_to = "";
                $index++;
                $type = $text[$index];
                $index++;
                while ($text[$index] != "*") {
                    $id_to .= $text[$index];
                    $index++;
                }
                // Destiny is a lexicon (id_lexico_to)
                if ($type == "l") {
                    // Origin is a lexicon (id_lexico_from -> id_lexico_to)
                    if (strcasecmp($type_from, 'lexico') == 0) {
                        //adds relationship lexicon to lexicon
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'l�xico para l�xico")</script>';
                      // Origin is a scenario (id_cenario -> id_lexico)
                    } else if (strcasecmp($type_from, 'cenario') == 0) {
                        //adds ratio setting for lexical
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para l�xico")</script>';
                        
                    }
                }
                // Target a scenario (id_cenario_to)
                if ($type == "c") {
                    // Origin a scenario (id_cenario_from -> id_cenario_to)
                    if (strcasecmp($type_from, 'cenario') == 0) {
                        echo '<script language="javascript">confirm(" ' . $id_from . ' - ' . $id_to . 'cen�rio para cen�rio")</script>';
                        // Relationships type scenario to scenario
                        // Adds relation of scenery to the scenery table centocen
                        //$q = "INSERT 
                        //		INTO centocen (id_cenario_from, id_cenario_to)
                        //		VALUES ($id_from, " . $vetor_cenarios[$j]->id_cenario . ")";
                        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
                $index + 1;
            }
        } elseif ($text[$index] == "}") {
            $parser--;
        }
        $index++;
    }
}

?>