<?php
session_start();
include("coloca_tags_xml.php");
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");
include("functionsBD/check_User_Authentication.php");

// Check if the user was autenticated
checkUserAuthentication("index.php");

// Test if user wnats a formated visualization or not
if (isset($_POST['flag'])) {
    $formated_flag = "ON";
} else {
    $formated_flag = "OFF";
}
?>

<?php

// gerador_xml.php
// Given the base and the id of the project, it generates the xml scenarios and lexicons
// Scenario - Generate XML reports
// Goal: Allow the administrator to generate reports in XML format to a project, identified by date.     
// Context: Menager wishes generate a report for one of the projects
//          Pre-condition: Login, registered project.
// Actors:    Administrator   
// Means:    System, data report, data registered of project, database.    
// Episode: The system provides to a screen where the administrator must provide the data
//         Report for subsequent identification, such as date and version.
//         To execute the report generation, simply click Generate.
// Restriction: The system performs two validations:
//         - If the date is valid.
//         - If there are scenarios and lexicons on dates equal to or earlier.
//         Generating the report successfully from the data registered design,
//         Provides the system administrator screen display XML report created,
//         Tags including internal links between lexicons and scenarios.
//         Constraint: Recovering data in the XML database and a XSL transform to display.      

function gerar_xml($bd, $id_projeto, $data_pesquisa, $flag_formatado) {
    if ($flag_formatado == "ON") {
        $xml_resultante = $xml_resultante . "<?xml-stylesheet type=''text/xsl'' href=''projeto.xsl''?>\n";
    }

    $xml_resultante = $xml_resultante . "<projeto>\n";

    // Select the project name

    $qry_nome = "SELECT nome
	                 FROM projeto
                     WHERE id_projeto = " . $id_projeto;
    $tb_nome = mysql_query($qry_nome) or die("Erro ao enviar a query de selecao.");

    $xml_resultante = $xml_resultante . "<nome>" . mysql_result($tb_nome, 0) . "</nome>\n";

    // Seleciona the scenarios of a project

    $qry_cenario = "SELECT id_cenario ,
                               titulo ,
                               objetivo ,
                               contexto ,
                               atores ,
                               recursos ,
                               episodios ,
                               excecao
                        FROM cenario
                        WHERE  (id_projeto = " . $id_projeto . ")
                        AND (data <=" . " '" . $data_pesquisa . "'" . ")
                        ORDER BY id_cenario,data DESC";

    $tb_cenario = mysql_query($qry_cenario) or die("Erro ao enviar a query de selecao.");
    $primeiro = true;

    $id_temp = "";
    $vetor_lex = carrega_vetor_todos($id_projeto);
    $vetor_cen = carrega_vetor_cenario_todos($id_projeto);

    while ($row = mysql_fetch_row($tb_cenario)) {
        $id_cenario = "<ID>" . $row[0] . "</ID>";
        if (($id_temp != $id_cenario) or (primeiro)) {
            $titulo = '<titulo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . ucwords(strip_tags($row[1])) . '</titulo>';

            $objetivo = "<objetivo>" . "<sentenca>" . faz_links_XML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</objetivo>";

            $contexto = "<contexto>" . "<sentenca>" . faz_links_XML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</contexto>";

            $atores = "<atores>" . "<sentenca>" . faz_links_XML(strip_tags($row[4]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</atores>";

            $recursos = "<recursos>" . "<sentenca>" . faz_links_XML(strip_tags($row[5]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</recursos>";

            $episodios = "<episodios>" . "<sentenca>" . faz_links_XML(strip_tags($row[6]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</episodios>";

            $excecao = "<excecao>" . "<sentenca>" . faz_links_XML(strip_tags($row[7]), $vetor_lex, $vetor_cen) . "</sentenca>" . "<PT/>" . "</excecao>";

            $xml_resultante = $xml_resultante . "<cenario>\n";

            // $xml_resultante = $xml_resultante . "$id_cenario\n" ;

            $xml_resultante = $xml_resultante . "$titulo\n";

            $xml_resultante = $xml_resultante . "$objetivo\n";

            $xml_resultante = $xml_resultante . "$contexto\n";

            $xml_resultante = $xml_resultante . "$atores\n";

            $xml_resultante = $xml_resultante . "$recursos\n";

            $xml_resultante = $xml_resultante . "$episodios\n";

            $xml_resultante = $xml_resultante . "$excecao\n";

            $xml_resultante = $xml_resultante . "</cenario>\n";

            $primeiro = false;

            //??$id_temp = id_cenario;
        }
    } // while
    // Select the lexicons of a project

    $qry_lexico = "SELECT id_lexico ,
		                        nome ,
                                nocao ,
                                impacto
                        FROM   lexico
                        WHERE  (id_projeto = " . $id_projeto . ")
                        AND (data <=" . " '" . $data_pesquisa . "'" . ")
                        ORDER BY id_lexico,data DESC";
    $tb_lexico = mysql_query($qry_lexico) or die("Erro ao enviar a query de selecao.");

    while ($row = mysql_fetch_row($tb_lexico)) {
        $id_lexico = "<ID>" . $row[0] . "</ID>";
        if (($id_temp != $id_lexico) or (primeiro)) {
            $nome = '<nome_simbolo name="' . strtr(strip_tags($row[1]), "����������", "aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags($row[1])) . '</texto>' . '</nome_simbolo>';

            $nocao = "<nocao>" . "<sentenca>" . faz_links_XML(strip_tags($row[2]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</nocao>";

            $impacto = "<impacto>" . "<sentenca>" . faz_links_XML(strip_tags($row[3]), $vetor_lex, $vetor_cen) . "<PT/>" . "</sentenca>" . "</impacto>";

            $xml_resultante = $xml_resultante . "<lexico>\n";

            // $xml_resultante = $xml_resultante . "$id_lexico\n" ;

            $xml_resultante = $xml_resultante . "$nome\n";

            $xml_resultante = $xml_resultante . "$nocao\n";

            $xml_resultante = $xml_resultante . "$impacto\n";

            $xml_resultante = $xml_resultante . "</lexico>\n";

            $primeiro = false;

            //$id_temp = id_lexico;
        }
    } // while

    $xml_resultante = $xml_resultante . "</projeto>\n";

    return $xml_resultante;
}

// gerar_xml
?>

<?php
$project_id = $_SESSION['id_projeto_corrente'];
$search_date = $data_ano . "-" . $data_mes . "-" . $data_dia;
$formated_flag = $flag;

// Open the data base
$database_work = bd_connect() or die("Erro ao conectar ao SGBD");

$qVerify = "SELECT * FROM publicacao WHERE id_projeto = '$project_id' AND versao = '$version' ";
$qrrVerify = mysql_query($qVerify);

if (!mysql_num_rows($qrrVerify)) {
    $str_xml = gerar_xml($database_work, $project_id, $search_date, $formated_flag);

    $xml_resultante = "<?xml version=''1.0'' encoding=''ISO-8859-1'' ?>\n" . $str_xml;
    $str_xml = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n" . $str_xml;

    $selection = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$project_id', '$search_date', '$version', '$xml_resultante')";

    //echo $q;

    mysql_query($selection) or die("Erro ao enviar a query INSERT!");

    $qq = "select * from publicacao where id_projeto = $project_id ";
    $qrr = mysql_query($qq) or die("Erro ao enviar a query");
    $row = mysql_fetch_row($qrr);
    $xml_bank = $row[3];

    // echo $xml_banco;

    $database_recover = bd_connect() or die("Erro ao conectar ao SGBD");
    $qRecupera = "SELECT * FROM publicacao WHERE id_projeto = '$project_id' AND versao = '$version'";
    $qrrRecupera = mysql_query($qRecupera) or die("Erro ao enviar a query de busca!");
    $row_recover = mysql_fetch_row($qrrRecupera);

    if ($formated_flag == "ON") {

        $xh = xslt_create();

        $args = array('/_xml' => $str_xml);

        $html = @xslt_process($xh, 'arg:/_xml', 'projeto.xsl', NULL, $args); //retirado o endere�o f�sico para o arquivo .xsl

        if (!( $html ))
            die("Erro ao processar o arquivo XML: " . xslt_error($xh));

        xslt_free($xh);

        $xml_bank = $row_recover[3];

        echo $xml_bank;

        //echo $html ;
    }
    else {
        /* $str_xml = str_replace ( "<", "<font color=\"red\">&lt;", $str_xml ) ;
          $str_xml = str_replace ( ">", "&gt;</font>", $str_xml ) ;
          $str_xml = str_replace ( "\n", "<br>", $str_xml ) ; */

        //<html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
        ?>
        <?
        echo $xml_bank;
        //</body></html>
        ?>
        <?php
    }
} else {
    ?>
    <html><head><title>Projeto</title></head><body style="background-color: #FFFFFF">
            <p style="color: red; font-weight: bold; text-align: center">Essa versao ja existe!</p>
            <br>
            <br>
            <h1 style="text-align:center;"><a href="JavaScript:window.history.go(-1)">Voltar</a></h1>
        </body></html>

    <?php
}
?>
