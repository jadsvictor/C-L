<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");


###################################################################
# Insere um cenario no banco de dados.
# Recebe o id_projeto, titulo, objetivo, contexto, atores, recursos, excecao e episodios. (1.1)
# Insere os valores do lexico na tabela CENARIO. (1.2)
# Devolve o id_cenario. (1.4)
#
###################################################################
if (!(function_exists("inclui_cenario"))) {

    function inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($titulo != NULL);
        assert($objetivo != NULL);
        assert($contexto!= NULL);
        assert($atores != NULL);
        assert($recursos != NULL);
        assert($excecao != NULL);
        assert($episodios != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($titulo));
        assert(is_string($objetivo));
        assert(is_string($contexto));
        assert(is_string($atores));
        assert(is_string($recursos));
        assert(is_string($excecao));
        assert(is_string($episodios));        
   
        //global $r;      // Conexao com a base de dados
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");

        $q = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios) 
		VALUES ($id_projeto,'$data', '" . prepares_data(strtolower($titulo)) . "', '" . prepares_data($objetivo) . "',
		'" . prepares_data($contexto) . "', '" . prepares_data($atores) . "', '" . prepares_data($recursos) . "',
		'" . prepares_data($excecao) . "', '" . prepares_data($episodios) . "')";

        mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT max(id_cenario) FROM cenario";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}


// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto, exceto o rec�m inserido:
//      2.1. Procurar em contexto e episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Incluir entrada na tabela 'centocen';
//      2.3. Procurar em contexto e episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.4. Se achar alguma ocorrencia:
//          2.4.1. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios, do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Incluir entrada na tabela 'centolex';

if (!(function_exists("adicionar_cenario"))) {

    function adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
    
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($titulo != NULL);
        assert($objetivo != NULL);
        assert($contexto!= NULL);
        assert($atores != NULL);
        assert($recursos != NULL);
        assert($excecao != NULL);
        assert($episodios != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($titulo));
        assert(is_string($objetivo));
        assert(is_string($contexto));
        assert(is_string($atores));
        assert(is_string($recursos));
        assert(is_string($excecao));
        assert(is_string($episodios));
        
        // Conecta ao SGBD
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        // Inclui o cenario na base de dados (sem transformar os campos, sem criar os relacionamentos)
        $id_incluido = inclui_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);

        $q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        ### PREENCHIMENTO DAS TABELAS LEXTOLEX E CENTOCEN PARA MONTAGEM DO MENU LATERAL
        // Verifica ocorr�ncias do titulo do cenario incluido no contexto 
        // e nos episodios de todos os outros cenarios e adiciona os relacionamentos,
        // caso possua, na tabela centocen

        while ($result = mysql_fetch_array($qrr)) {    // Para todos os cenarios
            $tituloEscapado = escapes_metacharacters($titulo);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
		                      VALUES (" . $result['id_cenario'] . ", $id_incluido)"; // (2.2.1)
                mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            $tituloEscapado = escapes_metacharacters($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $episodios) != 0)) {   // (2.3)        
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while
        // Verifica a ocorrencia do nome de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido 

        $q = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($qrr)) {    // (3)
            $nomeEscapado = escapes_metacharacters($result2['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $titulo) != 0) ||
                    (preg_match($regex, $objetivo) != 0) ||
                    (preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $atores) != 0) ||
                    (preg_match($regex, $recursos) != 0) ||
                    (preg_match($regex, $episodios) != 0) ||
                    (preg_match($regex, $excecao) != 0)) {   // (3.2)
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result2['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
        // Verifica a ocorrencia dos sinonimos de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido
        //Sinonimos

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }

        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_incluido";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            // verifica sinonimos dos outros lexicos no cenario inclu�do
            while ($result = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapes_metacharacters($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objetivo) != 0) ||
                        (preg_match($regex, $contexto) != 0) ||
                        (preg_match($regex, $atores) != 0) ||
                        (preg_match($regex, $recursos) != 0) ||
                        (preg_match($regex, $episodios) != 0) ||
                        (preg_match($regex, $excecao) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
                        mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }   
            }  
        } 
    }

}

###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeCenario"))) {

    function removeCenario($id_projeto, $id_cenario) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($id_cenario != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string(id_cenario));
        
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser removido
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser removido
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");
        # Remove o cenario escolhido
        $sql4->execute("DELETE FROM cenario WHERE id_cenario = $id_cenario");
    }

}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("alteraCenario"))) {

    function alteraCenario($id_projeto, $id_cenario, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($titulo != NULL);
        assert($objetivo != NULL);
        assert($contexto!= NULL);
        assert($atores != NULL);
        assert($recursos != NULL);
        assert($excecao != NULL);
        assert($episodios != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($titulo));
        assert(is_string($objetivo));
        assert(is_string($contexto));
        assert(is_string($atores));
        assert(is_string($recursos));
        assert(is_string($excecao));
        assert(is_string($episodios));
        
        $DB = new PGDB ();
        $sql1 = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);

        # Remove o relacionamento entre o cenario a ser alterado
        # e outros cenarios que o referenciam
        $sql1->execute("DELETE FROM centocen WHERE id_cenario_from = $id_cenario");
        $sql2->execute("DELETE FROM centocen WHERE id_cenario_to = $id_cenario");
        # Remove o relacionamento entre o cenario a ser alterado
        # e o seu lexico
        $sql3->execute("DELETE FROM centolex WHERE id_cenario = $id_cenario");

        # atualiza o cenario

        $sql4->execute("update cenario set 
		objetivo = '" . prepares_data($objetivo) . "', 
		contexto = '" . prepares_data($contexto) . "', 
		atores = '" . prepares_data($atores) . "', 
		recursos = '" . prepares_data($recursos) . "', 
		episodios = '" . prepares_data($episodios) . "', 
		excecao = '" . prepares_data($excecao) . "' 
		where id_cenario = $id_cenario ");

        // monta_relacoes($id_projeto);
        // Conecta ao SGBD
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $q = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_cenario
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // Para todos os cenarios
            $tituloEscapado = escapes_metacharacters($titulo);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) {   // (2.2)
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
	                      VALUES (" . $result['id_cenario'] . ", $id_cenario)"; // (2.2.1)
                mysql_query($q) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            $tituloEscapado = escapes_metacharacters($result['titulo']);
            $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $episodios) != 0)) {   // (2.3)        
                $q = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_cenario, " . $result['id_cenario'] . ")"; //(2.4.1)

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }   // if
        }   // while


        $q = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($qrr)) {    // (3)
            $nomeEscapado = escapes_metacharacters($result2['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $titulo) != 0) ||
                    (preg_match($regex, $objetivo) != 0) ||
                    (preg_match($regex, $contexto) != 0) ||
                    (preg_match($regex, $atores) != 0) ||
                    (preg_match($regex, $recursos) != 0) ||
                    (preg_match($regex, $episodios) != 0) ||
                    (preg_match($regex, $excecao) != 0)) {   // (3.2)
                $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = " . $result2['id_lexico'];
                $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayCen = mysql_fetch_array($qrCen);

                if ($resultArrayCen == false) {
                    $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, " . $result2['id_lexico'] . ")";
                    mysql_query($q) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                }
            }   // if
        }   // while
        //Sinonimos

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }

        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_cenario";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($qrr)) {    // verifica sinonimos dos lexicos no cenario inclu�do
                $nomeSinonimoEscapado = escapes_metacharacters($nomesSinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $objetivo) != 0) ||
                        (preg_match($regex, $contexto) != 0) ||
                        (preg_match($regex, $atores) != 0) ||
                        (preg_match($regex, $recursos) != 0) ||
                        (preg_match($regex, $episodios) != 0) ||
                        (preg_match($regex, $excecao) != 0)) {

                    $qCen = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = $id_lexicoSinonimo[$i] ";
                    $qrCen = mysql_query($qCen) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayCen = mysql_fetch_array($qrCen);

                    if ($resultArrayCen == false) {
                        $q = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, $id_lexicoSinonimo[$i])";
                        mysql_query($q) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                    }
                }  
            }   
        } 
    }

}

###################################################################
# Essa funcao recebe um id de conceito e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeConceito"))) {

    function removeConceito($id_projeto, $id_conceito) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($id_conceito != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_conceito));
        
        $DB = new PGDB ();
        $sql = new QUERY($DB);
        $sql2 = new QUERY($DB);
        $sql3 = new QUERY($DB);
        $sql4 = new QUERY($DB);
        $sql5 = new QUERY($DB);
        $sql6 = new QUERY($DB);
        $sql7 = new QUERY($DB);
        # Este select procura o cenario a ser removido
        # dentro do projeto

        $sql2->execute("SELECT * FROM conceito WHERE id_projeto = $id_projeto and id_conceito = $id_conceito");
        if ($sql2->getntuples() == 0) {
            //echo "<BR> Cenario nao existe para esse projeto." ;
        } else {
            $record = $sql2->gofirst();
            $nomeConceito = $record['nome'];
            # tituloCenario = Nome do cenario com id = $id_cenario
        }
        # [ATENCAO] Essa query pode ser melhorada com um join
        //print("<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto");
        /*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $tituloCenario");
          if ($sql->getntuples() == 0){
          echo "<BR> Projeto n�o possui cenarios." ;
          }else{ */
        $qr = "SELECT * FROM conceito WHERE id_projeto = $id_projeto AND id_conceito != $id_conceito";
        //echo($qr)."          ";
        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result = mysql_fetch_array($qrr)) {
            # Percorre todos os cenarios tirando as tag do conceito
            # a ser removido
            //$record = $sql->gofirst ();
            //while($record !='LAST_RECORD_REACHED'){
            $idConceitoRef = $result['id_conceito'];
            $nomeAnterior = $result['nome'];
            $descricaoAnterior = $result['descricao'];
            $namespaceAnterior = $result['namespace'];
            #echo        "/<a title=\"Cen�rio\" href=\"main.php?t='c'&id=$id_cenario>($tituloCenario)<\/a>/mi"  ;
            #$episodiosAnterior = "<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin</a>" ;
            /* "'<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
            $tiratag = "'<[\/\!]*?[^<>]*?>'si";
            //$tiratagreplace = "";
            //$tituloCenario = preg_replace($tiratag,$tiratagreplace,$tituloCenario);
            $regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi"; //rever
            $replace = "$1";
            //echo($episodiosAnterior)."   ";
            //$tituloAtual = $tituloAnterior ;
            //*$tituloAtual = preg_replace($regexp,$replace,$tituloAnterior);*/
            $descricaoAtual = preg_replace($regexp, $replace, $descricaoAnterior);
            $namespaceAtual = preg_replace($regexp, $replace, $namespaceAnterior);
            /* echo "ant:".$episodiosAtual ;
              echo "<br>" ;
              echo "dep:".$episodiosAnterior ; */
            // echo($tituloCenario)."   ";
            // echo($episodiosAtual)."  ";
            //print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
            $sql7->execute("update conceito set descricao = '$descricaoAtual', namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");

            //$record = $sql->gonext() ;
            // }
        }

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM conceito WHERE id_conceito = $id_conceito");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito");
    }

}

###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
function checarCenarioExistente($projeto, $titulo) {
    
    //tests if the variable is not null
    assert($projeto != NULL);
    assert($titulo != NULL);
    
    //tests if the variable has the correct type
    assert(is_string($projeto));
    assert(is_string($titulo));
    
    $naoexiste = false;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $q = "SELECT * FROM cenario WHERE id_projeto = $projeto AND titulo = '$titulo' ";
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo cenario ela deve receber os campos do novo
# cenario.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este cenario caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarCenario"))) {

    function inserirPedidoAdicionarCenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios, $id_usuario) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($titulo != NULL);
        assert($objetivo != NULL);
        assert($contexto!= NULL);
        assert($atores != NULL);
        assert($recursos != NULL);
        assert($excecao != NULL);
        assert($episodios != NULL);
        assert($id_usuario != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($titulo));
        assert(is_string($objetivo));
        assert(is_string($contexto));
        assert(is_string($atores));
        assert(is_string($recursos));
        assert(is_string($excecao));
        assert(is_string($episodios));
        assert(is_string($id_usuario));
        
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente
            $insere->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, '$titulo', '$objetivo', '$contexto', '$atores', '$recursos', '$excecao', '$episodios', $id_usuario, 'inserir', 0)");
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
                mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema $nome\nPede para inserir o cenario $titulo \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario"))) {

    function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($id_cenario != NULL);
        assert($id_usuario != NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_cenario));
        assert(is_string($id_usuario));
        
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $qr = mysql_query("SELECT * FROM participa WHERE gerente = 1 
            AND id_usuario =" . _GET('$id_usuario') ." 
            AND id_projeto =" . _GET('$id_projeto')) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //Nao e gerente
            $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $select->gofirst();
            $titulo = $cenario['titulo'];
            $insere->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, $id_cenario, '$titulo', $id_usuario, 'remover', 0)");
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
        } else {
            removeCenario($id_projeto, $id_cenario);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um conceito ela deve receber os campos do conceito
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {

    function inserirPedidoAlterarConceito($id_projeto, $id_conceito, $nome, $descricao, $namespace, $justificativa, $id_usuario) {
        
        //tests if the variable is not null
        assert($id_projeto != NULL);
        assert($id_conceito != NULL);
        assert($nome != NULL);
        assert($descricao != NULL);
        assert($namespace != NULL);
        assert($justificativa != NULL);
        assert($id_usuario!= NULL);
        
        //tests if the variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_conceito));
        assert(is_string($nome));
        assert(is_string($descricao));
        assert(is_string($namespace));
        assert(is_string($justificativa));
        assert(is_string($id_usuario));        
        
        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidocon (id_projeto, id_conceito, nome, descricao, namespace, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_conceito, '$nome', '$descricao', '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
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
                mail("$mailGerente", "Pedido de Altera��o Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $nome \nObrigado!", "From: $nomeUsuario\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        } else { //Eh gerente
            removeConceito($id_projeto, $id_conceito);
            adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
        }
    }

}

?>

