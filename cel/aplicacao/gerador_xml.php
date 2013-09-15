<?php
session_start();

include("funcoes_genericas.php");
include_once("coloca_links.php");
include("httprequest.inc");
include_once("bd.inc");
include_once("seguranca.php");

// Checa se o usuario foi autenticado

chkUser("index.php");        


if (isset($_POST['flag'])) {
    $flag = "ON";
} else {
    $flag = "OFF";
}
?>

<?php
// gerador_xml.php
// Dada a base e o id do projeto, gera-se o xml
// dos cen�rios e l�xicos.
//Cen�rio - Gerar Relat�rios XML 
//Objetivo:    Permitir ao administrador gerar relat�rios em formato XML de um projeto, identificados por data.     
//Contexto:    Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado.
//Atores:    Administrador     
//Recursos:    Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.     
//Epis�dios:O sistema fornece para o administrador uma tela onde dever� fornecer os dados
//          do relat�rio para sua posterior identifica��o, como data e vers�o. 
//          Para efetivar a gera��o do relat�rio, basta clicar em Gerar. 
//          Restri��o: O sistema executar� duas valida��es: 
//                      - Se a data � v�lida.
//                      - Se existem cen�rios e l�xicos em datas iguais ou anteriores.
//          Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
//          o sistema fornece ao administrador a tela de visualiza��o do relat�rio XML criado. 
//          Restri��o: Recuperar os dados em XML do Banco de dados e os transformar por uma XSL para a exibi��o.      

if (!(function_exists("gerar_xml"))) {

    function gerar_xml($database, $project_id, $search_date, $formated_flag) {
        $resultant_xml = "";
        $emptyVector = array();

        if ($formated_flag == "ON") {
            $resultant_xml = "";
            $resultant_xml = $resultant_xml . "<?xml-stylesheet type='text/xsl' href='projeto.xsl'?>\n";
        }
        $resultant_xml = $resultant_xml . "<projeto>\n";

        // Seleciona o nome do projeto

        $qry_name = "SELECT nome
                     FROM projeto
                     WHERE id_projeto = " . $project_id;
        $tb_name = mysql_query($qry_name) or die("Erro ao enviar a query de selecao.");

        // Adiciona o nome do projeto no xml		
        $resultant_xml = $resultant_xml . "<nome>" . mysql_result($tb_name, 0) . "</nome>\n";

        ## CEN�RIOS ##
        // Seleciona os cen�rios de um projeto.

        $qry_scenario = "SELECT id_cenario ,
                               titulo ,
                               objetivo ,
                               contexto ,
                               atores ,
                               recursos ,
                               episodios ,
                               excecao
                        FROM   cenario
                        WHERE  (id_projeto = " . $project_id
                . ") AND (data <=" . " '" . $search_date . "'" . ")
                        ORDER BY id_cenario,data DESC";

        $tb_scenario = mysql_query($qry_scenario) or die("Erro ao enviar a query de selecao.");

        $first = true;

        $id_temp = "";

        $vetor_todos_lexicos = carrega_vetor_lexicos($project_id, 0, false);

        // Para cada cen�rio

        while ($row = mysql_fetch_row($tb_scenario)) {
            $scenario_id = "<ID>" . $row[0] . "</ID>";
            $current_scenario_id = $row[0];
            $scenarios_vector = carrega_vetor_cenario($project_id, $current_scenario_id, true);

            // Porque usa $id_temp != $id_cenario ? e a variavel primeiro

            if (($id_temp != $scenario_id) or (primeiro)) {
                $title = '<titulo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

                $goal = "<objetivo>" . "<sentenca>" . generate_xml_links(monta_links($row[2], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</objetivo>";

                $context = "<contexto>" . "<sentenca>" . generate_xml_links(monta_links($row[3], $vetor_todos_lexicos, $scenarios_vector)) . "</sentenca>" . "<PT/>" . "</contexto>";

                $actors = "<atores>" . "<sentenca>" . generate_xml_links(monta_links($row[4], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</atores>";

                $means = "<recursos>" . "<sentenca>" . generate_xml_links(monta_links($row[5], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</recursos>";

                $exception = "<excecao>" . "<sentenca>" . generate_xml_links(monta_links($row[7], $vetor_todos_lexicos, $emptyVector)) . "</sentenca>" . "<PT/>" . "</excecao>";

                $episodes = "<episodios>" . "<sentenca>" . generate_xml_links(monta_links($row[6], $vetor_todos_lexicos, $scenarios_vector)) . "</sentenca>" . "<PT/>" . "</episodios>";

                $resultant_xml = $resultant_xml . "<cenario>\n";

                $resultant_xml = $resultant_xml . "$title\n";

                $resultant_xml = $resultant_xml . "$goal\n";

                $resultant_xml = $resultant_xml . "$context\n";

                $resultant_xml = $resultant_xml . "$actors\n";

                $resultant_xml = $resultant_xml . "$means\n";

                $resultant_xml = $resultant_xml . "$episodes\n";

                $resultant_xml = $resultant_xml . "$exception\n";

                $resultant_xml = $resultant_xml . "</cenario>\n";

                $first = false;

                //??$id_temp = id_cenario;
            }
        } // while dos cen�rios
        // Seleciona os lexicos de um projeto.

        $qry_lexicon = "SELECT id_lexico ,
                               nome ,
                               nocao ,
                               impacto
                        FROM   lexico
                        WHERE  (id_projeto = " . $project_id .
                ") AND (data <=" . " '" . $search_date . "'" . ")

                ORDER BY id_lexico,data DESC";

        $tb_lexicon = mysql_query($qry_lexicon) or die("Erro ao enviar a query de selecao.");

        $first = true;

        $id_temp = "";

        // Para cada simbolo do lexico

        while ($row = mysql_fetch_row($tb_lexicon)) {
            $lexicons_vector = carrega_vetor_lexicos($project_id, $row[0], true);
            quicksort($lexicons_vector, 0, count($lexicons_vector) - 1, 'lexico');
            $lexicon_id = "<ID>" . $row[0] . "</ID>";
            if (($id_temp != $lexicon_id) or (primeiro)) {

                $name = '<nome_simbolo id="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';


                // Consulta os sinonimos do simbolo
                $querySynonymous = "SELECT nome 
									FROM sinonimo
									WHERE (id_projeto = " . $project_id . ") 
									AND (id_lexico = " . $row[0] . " )";

                $resultSynonymous = mysql_query($querySynonymous) or die("Erro ao enviar a query de selecao de sinonimos.");

                //Para cada sinonimo do simbolo
                $synonymous = "<sinonimos>";

                while ($rowSin = mysql_fetch_row($resultSynonymous)) {
                    $synonymous .= "<sinonimo>" . $rowSin[0] . "</sinonimo>";
                }
                $synonymous .= "</sinonimos>";

                $notion = "<nocao>" . "<sentenca>" . generate_xml_links(monta_links($row[2], $lexicons_vector, $emptyVector)) . "<PT/>" . "</sentenca>" . "</nocao>";

                $impact = "<impacto>" . "<sentenca>" . generate_xml_links(monta_links($row[3], $lexicons_vector, $emptyVector)) . "<PT/>" . "</sentenca>" . "</impacto>";

                $resultant_xml = $resultant_xml . "<lexico>\n";

                $resultant_xml = $resultant_xml . "$name\n";

                $resultant_xml = $resultant_xml . "$synonymous\n";

                $resultant_xml = $resultant_xml . "$notion\n";

                $resultant_xml = $resultant_xml . "$impact\n";

                $resultant_xml = $resultant_xml . "</lexico>\n";

                $first = false;

                //$id_temp = id_lexico;
            }
        } // while

        $resultant_xml = $resultant_xml . "</projeto>\n";

        return $resultant_xml;
    }

// gerar_xml
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//
//Cen�rio - Gerar links nos Relat�rios XML criados
//
//Objetivo:    Permitir que os relat�rios gerados em formato XML possuam termos com links 
//          para os seus respectivos l�xicos
//
//Contexto:    Gerente deseja gerar um relat�rio em XML para um dos projetos da qual � administrador.
//          Pr�-Condi��o: Login, projeto cadastrado, acesso ao banco de dados.
//
//Atores:    Sistema    
//
//Recursos:    Sistema, senten�as a serem linkadas, dados cadastrados do projeto, banco de dados. 
//    
//Epis�dios:O sistema recebe a senten�a com os tags pr�prios do C&L e retorna o c�digo do link HTML
//            equivalente para os l�xicos cadatrados no sistema. 
//     
///////////////////////////////////////////////////////////////////////////////////////////////////
//
//L�xicos:
//
//     Fun��o:            gera_xml_links
//     Descri��o:         Analisa uma senten�a recebida afim de identificar as tags utilizadas no C&L
//                        para linkar os l�xicos e transformar em links XML.
//     Sin�nimos:         -
//     Exemplo: 
//        ENTRADA: <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre</a>
//                 <!--/CL-->
//        SA�DA:  <a title="Lexico" href="main.php?t=l&id=228"><texto referencia_lexico=software 
//                livre>software livre</texto></a>
//
//     Vari�vel:            $sentenca
//     Descri��o:         Armazena a express�o passada por argumento a ser tranformada em link.
//     Sin�nimos:         -
//     Exemplo:             <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre
//                        </a><!--/CL-->
//
//     Vari�vel:            $regex
//     Descri��o:            Armazena o pattern a ser utilizado ao se separar a senten�a.
//     Sin�nimos:            -
//     Exemplo:            "/(<!--CL:tam:\d+-->(<a[^>]*?\>)([^<]*?)<\/a><!--\/CL-->)/mi"
//
//     Vari�vel:            $vetor_texto
//     Descri��o:         Array que armazena palavra por palavra a sente�a a ser linkada, sem o tag.
//     Sin�nimos:         -
//     Exemplo:             $vetor_texto[0] => software
//                        $vetor_texto[1] => livre
//
//     Vari�vel:            $inside_tag
//     Descri��o:         Determina se a an�lise est� sendo feita dentro ou fora do tag
//     Sin�nimos:         -
//     Exemplo:             false
//
//     Vari�vel:            $tamanho_vetor_texto
//     Descri��o:         Armazena a n�mero de palavras que se encontram no array $vetor_texto. 
//     Sin�nimos:         -
//     Exemplo:             2
//
//     Vari�vel:            $i
//     Descri��o:         Vari�vel utilizada como um contador para uso gen�rico.
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $match
//     Descri��o:         Armazena o valor 1 caso a string "/href="main.php\?t=(.)&id=(\d+?)"/mi"
//                        seja encontrada na no array $vetor_texto. Caso contr�rio, armazena 0.
//     Sin�nimos:         -
//     Exemplo:             0
//
//     Vari�vel:            $id_projeto
//     Descri��o:         Armazena o n�mero identificador do projeto corrente.
//     Sin�nimos:         -
//     Exemplo:             1
//
//     Vari�vel:            $atributo
//     Descri��o:         Armazena um tag que indica a refer�ncia para um l�xico
//     Sin�nimos:         -
//     Exemplo:             referencia_lexico
//
//     Vari�vel:            $query
//     Descri��o:         Armazena a consulta a ser feita no banco de dados
//     Sin�nimos:         -
//     Exemplo:             SELECT nome FROM lexico WHERE id_projeto = $id_projeto
//
//     Vari�vel:            $result
//     Descri��o:         Armazena o resultado da consulta feita ao banco de dados
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $row
//     Descri��o:         Array que armazena tupla a tupla o resultado da consulta realizada
//     Sin�nimos:         -
//     Exemplo:             -
//
//     Vari�vel:            $valor
//     Descri��o:         Armazena uma tupla, substituindo os caracteres acentuados pelos seus 
//                        equivalentes sem acentua��o.
//     Sin�nimos:         -
//     Exemplo:             acentuacao
//
///////////////////////////////////////////////////////////////////////////////////////////////////


if (!(function_exists("gera_xml_links"))) {

    function generate_xml_links($sentence) {

        if (trim($sentence) != "") {

            $regex = "/(<a[^>]*?>)(.*?)<\/a>/";

            $text_vector = preg_split($regex, $sentence, -1, PREG_SPLIT_DELIM_CAPTURE);
            $text_vector_size = count($text_vector);
            $number = 0;


            while ($number < $text_vector_size) {
                preg_match('/href="main.php\?t=(.)&id=(\d+?)"/mi', $text_vector[$number], $match);
                if ($match) {
                    $project_id = $_SESSION['id_projeto_corrente'];

                    // Verifica se � l�xico 
                    if ($match[1] == 'l') {
                        // Retira o link do texto
                        $text_vector[$number] = "";

                        //link para l�xico
                        $attribute = "referencia_lexico";

                        $query = "SELECT nome FROM lexico WHERE id_projeto = $project_id AND id_lexico = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query lexico");
                        $row = mysql_fetch_row($result);
                        // Pega o nome do l�xico
                        $value = strtr($row[0], "����������", "aaaaoooeec");

                        $text_vector[$number + 1] = '<texto ' . $attribute . '="' . $value . '">' . $text_vector[$number + 1] . '</texto>';
                    } else if ($match[1] == 'c') {
                        // Retira o link do texto
                        $text_vector[$number] = "";

                        //link para cen�rio
                        $attribute = "referencia_cenario";

                        $query = "SELECT titulo FROM cenario WHERE id_projeto = $project_id AND id_cenario = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query cenario");
                        $row = mysql_fetch_row($result);
                        // Pega o titulo do cenario
                        $value = strtr($row[0], "����������", "aaaaoooeec");

                        $text_vector[$number + 1] = '<texto ' . $attribute . '="' . $value . '">' . strip_tags($text_vector[$number + 1]) . '</texto>';
                    }

                    $number = $number + 2;
                } else {
                    if (trim($text_vector[$number]) != "") {
                        $text_vector[$number] = "<texto>" . $text_vector[$number] . "</texto>";
                    }

                    $number = $number + 1;
                }
            }
            // Junta os elementos do array vetor_texto em uma string
            return implode("", $text_vector);
        }
        return $sentence;
    }

}
?>

<?php
$project_id = $_SESSION['id_projeto_corrente'];
$search_date = $data_ano . "-" . $data_mes . "-" . $data_dia;
$formated_flag = $flag;

// Abre base de dados.
$database_work = bd_connect() or die("Erro ao conectar ao SGBD");

$qVerify = "SELECT * FROM publicacao WHERE id_projeto = '$project_id' AND versao = '$version' ";
$qrrVerify = mysql_query($qVerify);

// Se n�o existir nenhum XML com o id passado ele cria
if (!mysql_num_rows($qrrVerify)) {

    $str_xml = gerar_xml($database_work, $project_id, $search_date, $formated_flag);

    $xml_resultante = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $selection = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$project_id', '$search_date', '$version', '" . mysql_real_escape_string($xml_resultante) . "')";

    mysql_query($selection) or die("Erro ao enviar a query INSERT do XML no banco de dados! ");
    recarrega("http://pes.inf.puc-rio.br/cel/aplicacao/mostraXML.php?id_projeto=" . $project_id . "&versao=" . $version);
} else {
    ?>
    <html><head><title>Projct</teitle></head><body bgcolor="#FFFFFF">
            <p style="color: red; font-weight: bold; text-align: center">Essa vers�o j� existe!</p>
            <br>
            <br>
        <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
    </body></html>

    <?php
}
?> 
