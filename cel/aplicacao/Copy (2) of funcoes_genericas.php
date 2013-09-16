<?php
include_once("bd.inc");
include_once("bd_class.php");

// Insere um lexico no banco de dados.
// Recebe o id_projeto, nome, no��o, impacto e os sinonimos. (1.1)
// Insere os valores do lexico na tabela LEXICO. (1.2)
// Insere todos os sinonimos na tabela SINONIMO. (1.3)
// Devolve o id_lexico. (1.4)

if (!(function_exists("chkUser"))) {

    function chkUser($url) {
        if (!(isset($_SESSION['id_usuario_corrente']))) {
            ?>

            <script language="javascript1.3">

                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,\n\
                     width=490,resizable,scrollbars,titlebar');

            </script>

            <?php
            exit();
        }
    }

}

if (!(function_exists("inclui_cenario"))) {

    function inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        //global $r;      // Conexao com a base de dados
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);

        $q = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios)
              VALUES ($id_projeto,'now', '" . strtolower($titulo) . "', 
                      '$objetivo', '$contexto', '$atores', '$recursos', 
                      '$excecao', '$episodios')";
        mysql_query($q) or die("Erro ao enviar a query<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT max(id_cenario) FROM cenario";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}

if (!(function_exists("inclui_lexico"))) {

    function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");
        $q = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" . strtolower($nome) . "',
                      '$nocao', '$impacto', '$classificacao')";
        mysql_query($q) or die("Erro ao enviar a query<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        //sinonimo
        $newLexId = mysql_insert_id($r);
        if (!is_array($sinonimos))
            $sinonimos = array();
        foreach ($sinonimos as $novoSin) {
            $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
            	VALUES ($newLexId, '" . strtolower($novoSin) . "', $id_projeto)";
            mysql_query($q, $r) or die("Erro ao enviar a query<br>" .
                            mysql_error() . "<br>" . __FILE__ . __LINE__);
        }
        $q = "SELECT max(id_lexico) FROM lexico";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}

if (!(function_exists("inclui_projeto"))) {

    function inclui_projeto($nome, $descricao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        //verifica se usuario ja existe
        $qv = "SELECT * FROM projeto WHERE nome = '$nome'";
        $qvr = mysql_query($qv) or die("Erro ao enviar a query de select<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        //$result = mysql_fetch_row($qvr);
        $resultArray = mysql_fetch_array($qvr);
        if ($resultArray != false) {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];
            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' 
                    AND id_usuario = '$id_usuario_corrente' ";
            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" .
                            mysql_error() . "<br>" . __FILE__ . __LINE__);
            $resultArray = mysql_fetch_row($qvuv);
            if ($resultArray[0] != null) {
                return -1;
            }
        }
        $q = "SELECT MAX(id_projeto) FROM projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        if ($result[0] == false) {
            $result[0] = 1;
        } else {
            $result[0]++;
        }
        $data = date("Y-m-d");
        $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
	              VALUES ($result[0],'$nome','$data' , '$descricao')";
        mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return $result[0];
    }

}

if (!(function_exists("replace_skip_tags"))) {

    function replace_skip_tags($search, $subject, $t_lnk, $id_lnk) {
        $title = ($t_lnk == "c") ? "Cenario" : "Lexico";
        $subject_tmp = preg_replace("/>(.*)(" . $search . ")(.*)</Ui", ">$1$2abcdef$3<", $subject);
        if ($t_lnk == "l") {
            $subject_tmp2 = preg_replace("/(\s|\b)(" . $search . ")(\s|\b)/i", '$1<a title="' . $title . '" href="main.php?t=' .
                    $t_lnk . '&id=' . $id_lnk . '">$2</a>$3', $subject_tmp);
        } else {
            $subject_tmp2 = preg_replace("/(\s|\b)(" . $search . ")(\s|\b)/i", '$1<a title="' . $title . '" href="main.php?t=' .
                    $t_lnk . '&id=' . $id_lnk . '">
                                         <span style="font-variant: small-caps">
                                         $2</span></a>$3', $subject_tmp);
        }
        $subject_tmp3 = preg_replace("/>(.*)(" . $search . ")abcdef(.*)</Ui", ">$1$2$3<", $subject_tmp2);
        ?>
        <?php
        ?>
        <?
        return $subject_tmp3;
    }

}

if (!(function_exists("recarrega"))) {

    function recarrega($url) {
        ?>
        <script language="javascript1.3">
            location.replace('<?= $url ?>');
        </script>
        <?php
    }

}

if (!(function_exists("breakpoint"))) {

    function breakpoint($num) {
        ?>
        <script language="javascript1.3">
            alert('<?= $num ?>');
        </script>
        <?php
    }

}

if (!(function_exists("simple_query"))) {

    function simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $q = "SELECT $field FROM $table WHERE $where";
        $qrr = mysql_query($q) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}

// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em contexto, episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Transformar a ocorrencia (titulo do cenario) em link;
//      2.3. Se algum campo sofreu alteracao:
//          2.3.1. Incluir entrada na tabela 'centocen';
//      2.4. Procurar em contexto, episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.5. Se achar alguma ocorrencia:
//          2.5.1. Transformar ocorrencia em link;
//          2.5.2. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Transformar as ocorrencias (nomes de termos) em link;
//      3.3. Se algum campo sofreu alteracao:
//          3.3.1. Incluir entrada na tabela 'centolex';

if (!(function_exists("adicionar_cenario"))) {

    function adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $id_incluido = inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
        $q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            $result_m = replace_skip_tags($titulo, $result, "c", $id_incluido);
            if ($result['contexto'] != $result_m['contexto'] ||
                    $result['episodios'] != $result_m['episodios']) {
                $q = "UPDATE cenario SET
                      contexto = '" . $result_m['contexto'] . "',
                      episodios = '" . $result_m['episodios'] . "'
                      WHERE id_cenario = " . $result['id_cenario'];
                mysql_query($q) or die("Erro ao enviar a query de UPDATE<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
                      VALUES (" . $result['id_cenario'] . ", $id_incluido)";
                mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            $result['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['titulo']);
            $contexto_m = replace_skip_tags($result['titulo'], $contexto, "c", $result['id_cenario']);
            $episodios_m = replace_skip_tags($result['titulo'], $episodios, "c", $result['id_cenario']);
            if ($contexto != $contexto_m ||
                    $episodios != $episodios_m) {
                $q = "UPDATE cenario SET
                      contexto = '$contexto_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
                mysql_query($q) or die("Erro ao enviar a query de UPDATE 2<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES 
                     ($id_incluido, " . $result['id_cenario'] . ")";
                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);  // (2.5.2)
                $contexto = $contexto_m;
                $episodios = $episodios_m;
            } 
        }  
        
        $q = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT 3<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {    // (3)
            $objetivo_m = replace_skip_tags($result['nome'], $objetivo, "l", $result['id_lexico']);
            $contexto_m = replace_skip_tags($result['nome'], $contexto, "l", $result['id_lexico']);
            $atores_m = replace_skip_tags($result['nome'], $atores, "l", $result['id_lexico']);
            $recursos_m = replace_skip_tags($result['nome'], $recursos, "l", $result['id_lexico']);
            $excecao_m = replace_skip_tags($result['nome'], $excecao, "l", $result['id_lexico']);
            $episodios_m = replace_skip_tags($result['nome'], $episodios, "l", $result['id_lexico']);
            if ($objetivo != $objetivo_m ||
                    $contexto != $contexto_m ||
                    $atores != $atores_m ||
                    $recursos != $recursos_m ||
                    $excecao != $excecao_m ||
                    $episodios != $episodios_m) {
                $q = "UPDATE cenario SET
                      objetivo  = '$objetivo_m',
                      contexto  = '$contexto_m',
                      atores    = '$atores_m',
                      recursos  = '$recursos_m',
                      excecao   = '$excecao_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
                mysql_query($q) or die("Erro ao enviar a query de UPDATE3<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);

                $qCen = "SELECT * FROM centolex WHERE id_cenario = 
                        $id_incluido AND id_lexico = " . $result['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>"
                                . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);
                if ($resultArrayCen == false) {
                    $q = "INSERT INTO centolex (id_cenario, id_lexico)
                         VALUES ($id_incluido, " . $result['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
                $objetivo = $objetivo_m;
                $contexto = $contexto_m;
                $atores = $atores_m;
                $recursos = $recursos_m;
                $excecao = $excecao_m;
                $episodios = $episodios_m;
            }
        }
        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = 
                      $id_projeto AND id_pedidolex = 0";
        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();
        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, 
               atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_incluido";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {
            $qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" .
                            mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($qrr)) {
                $objetivo_m = replace_skip_tags($nomesSinonimos[$i], $objetivo, 
                                                "l", $id_lexicoSinonimo[$i]);
                $contexto_m = replace_skip_tags($nomesSinonimos[$i], $contexto, 
                                                "l", $id_lexicoSinonimo[$i]);
                $atores_m = replace_skip_tags($nomesSinonimos[$i], $atores, 
                                                "l", $id_lexicoSinonimo[$i]);
                $recursos_m = replace_skip_tags($nomesSinonimos[$i], $recursos, 
                                                "l", $id_lexicoSinonimo[$i]);
                $excecao_m = replace_skip_tags($nomesSinonimos[$i], $excecao,
                                                "l", $id_lexicoSinonimo[$i]);
                $episodios_m = replace_skip_tags($nomesSinonimos[$i], $episodios,
                                                "l", $id_lexicoSinonimo[$i]);
                if ($objetivo != $objetivo_m ||
                        $contexto != $contexto_m ||
                        $atores != $atores_m ||
                        $recursos != $recursos_m ||
                        $excecao != $excecao_m ||
                        $episodios != $episodios_m) {
                    $q = "UPDATE cenario SET
                      objetivo  = '$objetivo_m',
                      contexto  = '$contexto_m',
                      atores    = '$atores_m',
                      recursos  = '$recursos_m',
                      excecao   = '$excecao_m',
                      episodios = '$episodios_m'
                      WHERE id_cenario = $id_incluido";
                    mysql_query($q) or die("Erro ao enviar a query de update 4<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $qCen = "SELECT * FROM centolex WHERE id_cenario = 
                            $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query
                                                       de select no centolex<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);
                    if ($resultArrayCen == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
                        mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                    $objetivo = $objetivo_m;
                    $contexto = $contexto_m;
                    $atores = $atores_m;
                    $recursos = $recursos_m;
                    $excecao = $excecao_m;
                    $episodios = $episodios_m;
                }
            }
        }
    }

}

if (!(function_exists("adicionar_lexico"))) {

    function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, 
              recursos, excecao, episodios
          FROM cenario
          WHERE id_projeto = $id_projeto";
        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            $result_m = replace_skip_tags($nome, $result, "l", $id_incluido);
            if ($result['objetivo'] != $result_m['objetivo'] ||
                    $result['contexto'] != $result_m['contexto'] ||
                    $result['atores'] != $result_m['atores'] ||
                    $result['recursos'] != $result_m['recursos'] ||
                    $result['excecao'] != $result_m['excecao'] ||
                    $result['episodios'] != $result_m['episodios']) {
                $q = "UPDATE cenario SET
                  objetivo = '" . $result_m['objetivo'] . "',
                  contexto = '" . $result_m['contexto'] . "',
                  atores = '" . $result_m['atores'] . "',
                  recursos = '" . $result_m['recursos'] . "',
                  excecao = '" . $result_m['excecao'] . "',
                  episodios = '" . $result_m['episodios'] . "'  
                  WHERE id_cenario = " . $result['id_cenario'];

                mysql_query($q) or die("Erro ao enviar a query de UPDATE 1<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
                $q = "INSERT INTO centolex (id_cenario, id_lexico)
                  VALUES (" . $result['id_cenario'] . ", $id_incluido)";

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" .
                            mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($qrr)) {
                $result_m2 = replace_skip_tags($sinonimos[$i], $result2, "l", $id_incluido);
                if ($result2['objetivo'] != $result_m2['objetivo'] ||
                        $result2['contexto'] != $result_m2['contexto'] ||
                        $result2['atores'] != $result_m2['atores'] ||
                        $result2['recursos'] != $result_m2['recursos'] ||
                        $result2['excecao'] != $result_m2['excecao'] ||
                        $result2['episodios'] != $result_m2['episodios']) {
                    $q = "UPDATE cenario SET
                  objetivo = '" . $result_m2['objetivo'] . "',                              
                  contexto = '" . $result_m2['contexto'] . "',                              
                  atores = '" . $result_m2['atores'] . "',                                  
                  recursos = '" . $result_m2['recursos'] . "',                              
                  excecao = '" . $result_m2['excecao'] . "',                                
                  episodios = '" . $result_m2['episodios'] . "'                             
                  WHERE id_cenario = " . $result2['id_cenario'];
                    mysql_query($q) or die("Erro ao enviar a query de UPDATE 2<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " .
                            $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query
                                                       de select no centolex<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);
                    if ($resultArraylex == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico)
                  VALUES (" . $result2['id_cenario'] . ", $id_incluido)";
                        mysql_query($q) or die("Erro ao enviar a query de INSERT 2<br>" .
                                        mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
            }
        }
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
          FROM lexico
          WHERE id_projeto = $id_projeto
          AND id_lexico != $id_incluido";
        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            $result_m = replace_skip_tags($nome, $result, "l", $id_incluido);
            if ($result['nocao'] != $result_m['nocao'] || $result['impacto'] != $result_m['impacto']) {
                $q = "UPDATE lexico SET
		                  nocao = '" . $result_m['nocao'] . "',
		                  impacto = '" . $result_m['impacto'] . "'
		                  WHERE id_lexico = '" . $result['id_lexico'] . "'";
                mysql_query($q) or die("Erro ao enviar a query de update no LEXICO 2<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] .
                        " AND id_lexico_to = $id_incluido";
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" .
                                mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);
                if ($resultArraylex == false) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
		                  VALUES (" . $result['id_lexico'] . ", $id_incluido)";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }
            $nocao_m = replace_skip_tags($result['nome'], $nocao, "l", $result['id_lexico']);
            $impacto_m = replace_skip_tags($result['nome'], $impacto, "l", $result['id_lexico']);
            if ($nocao_m != $nocao || $impacto_m != $impacto) {
                $q = "UPDATE lexico SET nocao = '$nocao_m', impacto = '$impacto_m' WHERE id_lexico = $id_incluido";
                mysql_query($q) or die("Erro ao executar query de update no lexico 4<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = $id_incluido AND id_lexico_to = " . $result['id_lexico'];
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);
                if ($resultArraylex == false) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES ($id_incluido, " . $result['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao executar query de insert no lextolex 3<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
                $nocao = $nocao_m;
                $impacto = $impacto_m;
            }
        }
        $ql = "SELECT id_lexico, nome, nocao, impacto
          FROM lexico
          WHERE id_projeto = $id_projeto
          AND id_lexico != $id_incluido";
        $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            while ($resultl = mysql_fetch_array($qrr)) {
                $result_ml = replace_skip_tags($sinonimos[$i], $resultl, "l", $id_incluido);
                if ($resultl['nocao'] != $result_ml['nocao'] ||
                        $resultl['impacto'] != $result_ml['impacto']) {
                    $q = "UPDATE lexico SET
	                  nocao = '" . $result_ml['nocao'] . "',                            
	                  impacto = '" . $result_ml['impacto'] . "'                         
	                  WHERE id_lexico = " . $resultl['id_lexico'];
                    mysql_query($q) or die("Erro ao enviar a query de update no lexico<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " .
                            $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de 
                                                        select no lextolex<br>" .
                                    mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);
                    if ($resultArraylex == false) {
                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";
                        mysql_query($q) or die("Erro ao enviar a query de insert no lextolex<br>" .
                                        mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
            }
        }
        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = 
                       $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";
        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query
                                                         de select no sinonimo<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();
        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }

}

if (!(function_exists("removeCenario"))) {

    function removeCenario($id_projeto, $id_cenario) {
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        $sql5 = new QUERY($DB);
        $sql6 = new QUERY($DB);
        $sql7 = new QUERY($DB);
        $sql2->execute("SELECT * FROM cenario WHERE id_projeto = 
                       $id_projeto and id_cenario = $id_cenario");
        if ($sql2->getntuples() == 0) {
            
        } else {
            $record = $sql2->gofirst();
            $tituloCenario = $record['titulo'];
        }
        $qr = "SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $id_cenario";
        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            $idCenarioRef = $result['id_cenario'];
            $tituloAnterior = $result['titulo'];
            $objetivoAnterior = $result['objetivo'];
            $contextoAnterior = $result['contexto'];
            $atoresAnterior = $result['atores'];
            $recursosAnterior = $result['recursos'];
            $episodiosAnterior = $result['episodios'];
            $excecaoAnterior = $result['excecao'];
            $tiratag = "'<[\/\!]*?[^<>]*?>'si";
            $regexp = "/<a[^>]*?>($tituloCenario)<\/a>/mi";
            $replace = "$1";
            $objetivoAtual = preg_replace($regexp, $replace, $objetivoAnterior);
            $contextoAtual = preg_replace($regexp, $replace, $contextoAnterior);
            $atoresAtual = preg_replace($regexp, $replace, $atoresAnterior);
            $recursosAtual = preg_replace($regexp, $replace, $recursosAnterior);
            $episodiosAtual = preg_replace($regexp, $replace, $episodiosAnterior);
            $excecaoAtual = preg_replace($regexp, $replace, $excecaoAnterior);
            $sql7->execute("update cenario set objetivo = '$objetivoAtual',
                           contexto = '$contextoAtual',atores = '$atoresAtual',
                           recursos = '$recursosAtual', episodios = '$episodiosAtual', 
                           excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
        }
        $sql3->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql4->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        $sql5->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");
        $sql6->execute("DELETE FROM cenario WHERE id_cenario = $id_cenario");
    }

}

if (!(function_exists("removeLexico"))) {

    function removeLexico($id_projeto, $id_lexico) {
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $update = new QUERY($DB);
        $delete = new QUERY($DB);
        $sql->execute("SELECT * FROM lexico WHERE id_projeto = $id_projeto and id_lexico = $id_lexico ");
        if ($sql->getntuples() == 0) {
            
        } else {
            $record = $sql->gofirst();
            $nomeLexico = $record['nome'];
        }
        $sql->execute("SELECT * FROM lexico WHERE id_projeto = $id_projeto ");
        if ($sql->getntuples() == 0) {
            
        } else {
            $record = $sql->gofirst();
            while ($record != 'LAST_RECORD_REACHED') {
                $idLexicoRef = $record['id_lexico'];
                $nocaoAnterior = $record['nocao'];
                $impactoAnterior = $record['impacto'];
                $regexp = "/<a[^>]*?>($nomeLexico)<\/a>/mi";
                $replace = "$1";
                $nocaoAtual = preg_replace($regexp, $replace, $nocaoAnterior);
                $impactoAtual = preg_replace($regexp, $replace, $impactoAnterior);
                $update->execute("update lexico set nocao = '$nocaoAtual',impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
                $record = $sql->gonext();
            }
        }
        $sql->execute("SELECT * FROM cenario WHERE id_projeto = $id_projeto ");
        if ($sql->getntuples() == 0) {
            
        } else {
            $record = $sql->gofirst();
            while ($record != 'LAST_RECORD_REACHED') {
                $idCenarioRef = $record['id_cenario'];
                $objetivoAnterior = $record['objetivo'];
                $contextoAnterior = $record['contexto'];
                $atoresAnterior = $record['atores'];
                $recursosAnterior = $record['recursos'];
                $episodiosAnterior = $record['episodios'];
                $excecaoAnterior = $record['excecao'];
                $regexp = "/<a[^>]*?>($nomeLexico)<\/a>/mi";
                $replace = "$1";
                $objetivoAtual = preg_replace($regexp, $replace, $objetivoAnterior);
                $contextoAtual = preg_replace($regexp, $replace, $contextoAnterior);
                $atoresAtual = preg_replace($regexp, $replace, $atoresAnterior);
                $recursosAtual = preg_replace($regexp, $replace, $recursosAnterior);
                $episodiosAtual = preg_replace($regexp, $replace, $episodiosAnterior);
                $excecaoAtual = preg_replace($regexp, $replace, $excecaoAnterior);
                $update->execute("update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual', atores = '$atoresAtual', recursos = '$recursosAtual', episodios = '$episodiosAtual', excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
                $record = $sql->gonext();
            }
        }
        $qSinonimos = "SELECT * FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico = $id_lexico";
        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de 
                                                        select no sinonimo<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        $nomesSinonimos = array();
        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomesSinonimos[] = $rowSinonimo["nome"];
        }
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {
            $sql->execute("SELECT * FROM lexico WHERE id_projeto = $id_projeto ");
            if ($sql->getntuples() == 0) {
                
            } else {
                $record = $sql->gofirst();
                $sinonimoProcura = $nomesSinonimos[$i];
                while ($record != 'LAST_RECORD_REACHED') {
                    $idLexicoRef = $record['id_lexico'];
                    $nocaoAnterior = $record['nocao'];
                    $impactoAnterior = $record['impacto'];
                    $regexp = "/<a[^>]*?>($sinonimoProcura)<\/a>/mi";
                    $replace = "$1";
                    $nocaoAtual = preg_replace($regexp, $replace, $nocaoAnterior);
                    $impactoAtual = preg_replace($regexp, $replace, $impactoAnterior);
                    $update->execute("update lexico set nocao = '$nocaoAtual',
                                     impacto = '$impactoAtual' where id_lexico = $idLexicoRef ");
                    $record = $sql->gonext();
                }
            }
        }
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {
            $sql->execute("SELECT * FROM cenario WHERE id_projeto = $id_projeto ");
            if ($sql->getntuples() == 0) {
                
            } else {
                $record = $sql->gofirst();
                while ($record != 'LAST_RECORD_REACHED') {
                    $idCenarioRef = $record['id_cenario'];
                    $objetivoAnterior = $record['objetivo'];
                    $contextoAnterior = $record['contexto'];
                    $atoresAnterior = $record['atores'];
                    $recursosAnterior = $record['recursos'];
                    $episodiosAnterior = $record['episodios'];
                    $excecaoAnterior = $record['excecao'];
                    $sinonimoProcura = $nomesSinonimos[$i];
                    $regexp = "/<a[^>]*?>($sinonimoProcura)<\/a>/mi";
                    $replace = "$1";
                    $objetivoAtual = preg_replace($regexp, $replace, $objetivoAnterior);
                    $contextoAtual = preg_replace($regexp, $replace, $contextoAnterior);
                    $atoresAtual = preg_replace($regexp, $replace, $atoresAnterior);
                    $recursosAtual = preg_replace($regexp, $replace, $recursosAnterior);
                    $episodiosAtual = preg_replace($regexp, $replace, $episodiosAnterior);
                    $excecaoAtual = preg_replace($regexp, $replace, $excecaoAnterior);
                    $update->execute("update cenario set objetivo = '$objetivoAtual',
                                     contexto = '$contextoAtual', atores = '$atoresAtual',
                                     recursos = '$recursosAtual', episodios = '$episodiosAtual',
                                     excecao = '$excecaoAtual' where id_cenario = $idCenarioRef ");
                    $record = $sql->gonext();
                }
            }
        }
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
    }

}

if (!(function_exists("removeConceito"))) {

    function removeConceito($id_projeto, $id_conceito) {
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        $sql5 = new QUERY($DB);
        $sql6 = new QUERY($DB);
        $sql7 = new QUERY($DB);
        $sql2->execute("SELECT * FROM conceito WHERE id_projeto = $id_projeto and
                       id_conceito = $id_conceito");
        if ($sql2->getntuples() == 0) {
            
        } else {
            $record = $sql2->gofirst();
            $nomeConceito = $record['nome'];
        }
        $qr = "SELECT * FROM conceito WHERE id_projeto = $id_projeto AND id_conceito != $id_conceito";
        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            $idConceitoRef = $result['id_conceito'];
            $nomeAnterior = $result['nome'];
            $descricaoAnterior = $result['descricao'];
            $namespaceAnterior = $result['namespace'];
            $tiratag = "'<[\/\!]*?[^<>]*?>'si";
            $regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi";
            $replace = "$1";
            $descricaoAtual = preg_replace($regexp, $replace, $descricaoAnterior);
            $namespaceAtual = preg_replace($regexp, $replace, $namespaceAnterior);
            $sql7->execute("update conceito set descricao = '$descricaoAtual', 
                           namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");
        }
        $sql6->execute("DELETE FROM conceito WHERE id_conceito = $id_conceito");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito");
    }

}

if (!(function_exists("removeRelacao"))) {

    function removeRelacao($id_projeto, $id_relacao) {
        $DB = new PGDB ();
        $sql6 = new QUERY($DB);
        $sql6->execute("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }

}

function checarLexicoExistente($projeto, $nome) {
    $naoexiste = false;
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$nome' ";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }
    $q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$nome' ";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray != false) {
        $naoexiste = false;
    }
    return $naoexiste;
}

function checarSinonimo($projeto, $listSinonimo) {
    $naoexiste = true;
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                    "<br>" . __FILE__ . __LINE__);
    foreach ($listSinonimo as $sinonimo) {
        $q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
        $q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
    }
    return $naoexiste;
}

function checarCenarioExistente($projeto, $titulo) {
    $naoexiste = false;
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                    "<br>" . __FILE__ . __LINE__);
    $q = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$titulo' ";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no cenario<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }
    return $naoexiste;
}

if (!(function_exists("inserirPedidoAdicionarCenario"))) {

    function inserirPedidoAdicionarCenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray == false) {
            $insere->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, 
                                                     contexto, atores, recursos, 
                                                     excecao, episodios, id_usuario,
                                                     tipo_pedido, aprovado) 
                                                     VALUES ($id_projeto, '$titulo', 
                                                     '$objetivo', '$contexto', '$atores',
                                                     '$recursos', '$excecao', '$episodios', 
                                                      $id_usuario, 'inserir', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema 
                      $nome\nPede para inserir o cenario $titulo \nObrigado!", "From: $nome\r\n" .
                        "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else {
            adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
        }
    }

}

if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario 
              AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray == false) {
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, 
                              objetivo, contexto, atores, recursos, excecao, episodios,
                              id_usuario, tipo_pedido, aprovado, justificativa) 
                              VALUES ($id_projeto, $id_cenario, '$titulo', '$objetivo',
                                      '$contexto', '$atores', '$recursos', '$excecao', 
                                      '$episodios', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Cen�rio", "O usuario do sistema 
                      $nome\nPede para alterar o cenario $titulo \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else {
            removeCenario($id_projeto, $id_cenario);
            adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
        }
    }

}

if (!(function_exists("inserirPedidoRemoverCenario"))) {

    function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
        $cenario = $select->gofirst();
        $titulo = $cenario['titulo'];
        $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, 
                          id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto,
                          $id_cenario, '$titulo', $id_usuario, 'remover', 0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
        $record = $select->gofirst();
        $nome = $record['nome'];
        $email = $record['email'];
        $record2 = $select2->gofirst();
        while ($record2 != 'LAST_RECORD_REACHED') {
            $id = $record2['id_usuario'];
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
            $record = $select->gofirst();
            $mailGerente = $record['email'];
            mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
            $record2 = $select2->gonext();
        }
    }

}

if (!(function_exists("inserirPedidoAdicionarLexico"))) {

    function inserirPedidoAdicionarLexico($id_projeto, $nome, $nocao, $impacto, $id_usuario, $sinonimos, $classificacao) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray == false) {
            $insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,
                              tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,
                              '$nome','$nocao','$impacto','$classificacao',$id_usuario,'inserir',0)");
            $newId = $insere->getLastId();
            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");
            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
                                  VALUES ($newId, '$sin', $id_projeto)");
            }
            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {
                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico 
                         $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else {
            adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }

}

if (!(function_exists("inserirPedidoAlterarLexico"))) {

    function inserirPedidoAlterarLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $justificativa, $id_usuario, $sinonimos, $classificacao) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = 
             $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray == false) {
            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,
                              nocao,impacto,id_usuario,tipo_pedido,aprovado,
                              justificativa, tipo) 
                              VALUES ($id_projeto,$id_lexico,'$nome','$nocao',
                                      '$impacto',$id_usuario,'alterar',0,'$justificativa',
                                      '$classificacao')");
            $newPedidoId = $insere->getLastId();
            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
                                  VALUES ($newPedidoId,'$sin', $id_projeto)");
            }
            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and 
                               id_projeto = $id_projeto");
            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {
                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Alterar L�xico", "O usuario do sistema $nome2\nPede para alterar o lexico 
                         $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else {
            removeLexico($id_projeto, $id_lexico);
            adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }

}

if (!(function_exists("inserirPedidoRemoverLexico"))) {

    function inserirPedidoRemoverLexico($id_projeto, $id_lexico, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico");
        $lexico = $select->gofirst();
        $nome = $lexico['nome'];
        $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,
                          tipo_pedido,aprovado)
                          VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");
        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } else {
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\n
                     Pede para remover o lexico $id_lexico \nObrigado!", "From: $nome\r\n" .
                        "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}

if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarConceito($id_projeto, $id_conceito, $nome, $descricao, $namespace, $justificativa, $id_usuario) {
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario 
              AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray == false) {
            $insere->execute("INSERT INTO pedidocon (id_projeto, id_conceito, 
                              nome, descricao, namespace, id_usuario, tipo_pedido, 
                              aprovado, justificativa) 
                              VALUES ($id_projeto, $id_conceito, '$nome', '$descricao',
                                      '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nomeUsuario = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito 
                     $nome \nObrigado!", "From: $nomeUsuario\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else {
            removeConceito($id_projeto, $id_conceito);
            adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
        }
    }

}

if (!(function_exists("inserirPedidoRemoverConceito"))) {

    function inserirPedidoRemoverConceito($id_projeto, $id_conceito, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito");
        $conceito = $select->gofirst();
        $nome = $conceito['nome'];
        $insere->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,
                          id_usuario,tipo_pedido,aprovado) 
                          VALUES ($id_projeto,$id_conceito,'$nome',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");
        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } else {
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\n
                      Pede para remover o conceito $id_conceito \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}

if (!(function_exists("inserirPedidoRemoverRelacao"))) {

    function inserirPedidoRemoverRelacao($id_projeto, $id_relacao, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao");
        $relacao = $select->gofirst();
        $nome = $relacao['nome'];

        $insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,
                          tipo_pedido,aprovado) 
                          VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)");
        $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
        $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");
        if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
        } else {
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while ($record2 != 'LAST_RECORD_REACHED') {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\n
                      Pede para remover o conceito $id_relacao \nObrigado!", "From: $nome\r\n" .
                        "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}

if (!(function_exists("tratarPedidoCenario"))) {

    function tratarPedidoCenario($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_cenario = $record['id_cenario'];
                $id_projeto = $record['id_projeto'];
                removeCenario($id_projeto, $id_cenario);
            } else {
                $id_projeto = $record['id_projeto'];
                $titulo = $record['titulo'];
                $objetivo = $record['objetivo'];
                $contexto = $record['contexto'];
                $atores = $record['atores'];
                $recursos = $record['recursos'];
                $excecao = $record['excecao'];
                $episodios = $record['episodios'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_cenario'];
                    removeCenario($id_projeto, $id_cenario);
                }
                adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
            }
        }
    }

}

if (!(function_exists("tratarPedidoLexico"))) {

    function tratarPedidoLexico($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $selectSin = new QUERY($DB);
        $select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_lexico = $record['id_lexico'];
                $id_projeto = $record['id_projeto'];
                removeLexico($id_projeto, $id_lexico);
            } else {
                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];
                $nocao = $record['nocao'];
                $impacto = $record['impacto'];
                $classificacao = $record['tipo'];
                $sinonimos = array();
                $selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $selectSin->gofirst();
                while ($sinonimo != 'LAST_RECORD_REACHED') {
                    $sinonimos[] = $sinonimo["nome"];
                    $sinonimo = $selectSin->gonext();
                }
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_lexico = $record['id_lexico'];
                    removeLexico($id_projeto, $id_lexico);
                }
                if (($idLexicoConflitante = adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)) <= 0) {
                    $idLexicoConflitante = -1 * $idLexicoConflitante;
                    $selectLexConflitante->execute("SELECT nome FROM lexico 
                                                    WHERE id_lexico = " . $idLexicoConflitante);
                    $row = $selectLexConflitante->gofirst();
                    return $row["nome"];
                }
            }
            return null;
        }
    }

}

if (!(function_exists("tratarPedidoConceito"))) {

    function tratarPedidoConceito($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_conceito = $record['id_conceito'];
                $id_projeto = $record['id_projeto'];
                removeConceito($id_projeto, $id_conceito);
            } else {
                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];
                $descricao = $record['descricao'];
                $namespace = $record['namespace'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_conceito'];
                    removeConceito($id_projeto, $id_conceito);
                }
                adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
            }
        }
    }

}

if (!(function_exists("tratarPedidoRelacao"))) {

    function tratarPedidoRelacao($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_relacao = $record['id_relacao'];
                $id_projeto = $record['id_projeto'];
                removeRelacao($id_projeto, $id_relacao);
            } else {
                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_relacao = $record['id_relacao'];
                    removeRelacao($id_projeto, $id_relacao);
                }
                adicionar_relacao($id_projeto, $nome);
            }
        }
    }

}

if (!(function_exists("verificaGerente"))) {
    verificaGerente($id_usuario);
}

if (!(function_exists("formataData"))) {

    function formataData($data) {
        $novaData = substr($data, 8, 9) .
                substr($data, 4, 4) .
                substr($data, 0, 4);
        return $novaData;
    }

}

if (!(function_exists("is_admin"))) {

    function is_admin($id_usuario, $id_projeto) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        $q = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto
              AND gerente = 1";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($qrr));
    }

}
if (!(function_exists("check_proj_perm"))) {

    function check_proj_perm($id_usuario, $id_projeto) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        $q = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() .
                        "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($qrr));
    }

}

function verificaGerente($id_usuario, $id_projeto) {
    $ret = 0;
    $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario 
          AND id_projeto = $id_projeto";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray != false) {
        $ret = 1;
    }
    return $ret;
}

function removeProjeto($id_projeto) {
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() .
                    "<br>" . __FILE__ . __LINE__);

    $qv = "Delete FROM pedidocen WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoCenario = mysql_query($qv) or die("Erro ao apagar pedidos de cenario<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "Delete FROM pedidolex WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "SELECT * FROM lexico WHERE id_projeto = '$id_projeto' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no lexico<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_lexico'];

        $qv = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' 
               OR id_lexico_to = '$id_lexico' ";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do lextolex<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM sinonimo WHERE id_projeto = '$id_projeto'";
        $deletacentolex = mysql_query($qv) or die("Erro ao apagar sinonimo<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM lexico WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do lexico<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "SELECT * FROM cenario WHERE id_projeto = '$id_projeto' ";
    $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no cenario<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArrayCenario = mysql_fetch_array($qvr);

    while ($result = mysql_fetch_array($qvr)) {
        $id_lexico = $result['id_cenario'];

        $qv = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario'
              OR id_cenario_to = '$id_cenario' ";
        $deletaCentoCen = mysql_query($qv) or die("Erro ao apagar pedidos do centocen<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);

        $qv = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
        $deletaLextoLe = mysql_query($qv) or die("Erro ao apagar pedidos do centolex<br>" .
                        mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $qv = "Delete FROM cenario WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($qv) or die("Erro ao apagar pedidos do cenario<br>" .
                    mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "Delete FROM participa WHERE id_projeto = '$id_projeto' ";
    $deletaParticipantes = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "Delete FROM publicacao WHERE id_projeto = '$id_projeto' ";
    $deletaPublicacao = mysql_query($qv) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    $qv = "Delete FROM projeto WHERE id_projeto = '$id_projeto' ";
    $deletaProjeto = mysql_query($qv) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}
?>

