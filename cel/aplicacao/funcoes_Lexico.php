<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");


###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, no��o, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
#
###################################################################
if (!(function_exists("inclui_lexico"))) {

    function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");


        $q = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" . prepares_data(strtolower($nome)) . "',
			  '" . prepares_data($nocao) . "', '" . prepares_data($impacto) . "', '$classificacao')";

        mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //sinonimo
        $newLexId = mysql_insert_id($r);


        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($newLexId, '" . prepares_data(strtolower($novoSin)) . "', $id_projeto)";
            mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        $q = "SELECT max(id_lexico) FROM lexico";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}

//
// Para a correta inclusao de um termo no lexico, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo termo na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em titulo, objetivo, contexto, recursos, atores, episodios
//           por ocorrencias do termo incluido ou de seus sinonimos;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//              2.2.1. Incluir entrada na tabela 'centolex';
// 3. Para todos termos do lexico daquele projeto (menos o recem-inserido):
//      3.1. Procurar em nocao, impacto por ocorrencias do termo inserido ou de seus sinonimos;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//              3.2.1. Incluir entrada na tabela 'lextolex';
//      3.3. Procurar em nocao, impacto do termo inserido por
//           ocorrencias de termos do lexico do mesmo projeto;
//      3.4. Se achar alguma ocorrencia:
//              3.4.1. Incluir entrada na table 'lextolex';

if (!(function_exists("adicionar_lexico"))) {

    function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)

        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";

        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapes_metacharacters($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $q = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_incluido)"; //2.2.1

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }


        //sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {

                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";

                        mysql_query($q) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } //if
                }//if                
            }//while            
        } //for


        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico != $id_incluido";

        //pega todos os outros lexicos
        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // (3)
            $nomeEscapado = escapes_metacharacters($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {

                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);

                if ($resultArraylex == false) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";

                    mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }

            $nomeEscapado = escapes_metacharacters($result['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {   // (3.3)        
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")";

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }   // while
        //lexico para lexico

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico != $id_incluido";

        //sinonimos dos outros lexicos no texto do inserido

        $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            while ($resultl = mysql_fetch_array($qrr)) {

                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";

                        mysql_query($q) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }//if
                }    //if
            }//while
        }//for
        //sinonimos ja existentes

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }

}

###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico"))) {

    function removeLexico($id_projeto, $id_lexico) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

        # Remove o relacionamento entre o lexico a ser removido
        # e outros lexicos que o referenciam
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove o lexico escolhido
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
    }

}

###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("alteraLexico"))) {

    function alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

        # Remove os relacionamento existentes anteriormente
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove todos os sinonimos cadastrados anteriormente
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");

        # Altera o lexico escolhido
        $delete->execute("UPDATE lexico SET 
		nocao = '" . prepares_data($nocao) . "', 
		impacto = '" . prepares_data($impacto) . "', 
		tipo = '$classificacao' 
		where id_lexico = $id_lexico");

        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        # Fim altera lexico escolhido
        ### VERIFICACAO DE OCORRENCIA EM CENARIOS ###
        
        # Verifica se h� alguma ocorrencia do titulo do lexico nos cenarios existentes no banco
        $qr = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";

        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapes_metacharacters($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $q = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_lexico)"; //2.2.1

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }
        # Fim da verificacao
        
        # Verifica se h� alguma ocorrencia de algum dos sinonimos do lexico nos cenarios existentes no banco
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {//Para cada sinonimo
            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($qrr)) {// para cada cenario

                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {
                }               
            }           
        } 
        # Fim da verificacao
        ########
        ### VERIFICACAO DE OCORRENCIA EM LEXICOS
        ########
        # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        # Verifica a ocorrencia do titulo dos outros lexicos no lexico alterado
        //select para pegar todos os outros lexicos
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico <> $id_lexico";

        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        while ($result = mysql_fetch_array($qrr)) { // para cada lexico exceto o que esta sendo alterado    // (3)
            # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
            $nomeEscapado = escapes_metacharacters($nome);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                      	VALUES (" . $result['id_lexico'] . ", $id_lexico)";

                mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            # Verifica a ocorrencia do titulo dos outros lexicos no texto do lexico alterado

            $nomeEscapado = escapes_metacharacters($result['nome']);
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {   // (3.3)        
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                		VALUES ($id_lexico, " . $result['id_lexico'] . ")";

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }// while
        # Fim da verificao por titulo

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico <> $id_lexico";

        # Verifica a ocorrencia dos sinonimos do lexico alterado nos outros lexicos

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {// para cada sinonimo do lexico alterado

            $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($resultl = mysql_fetch_array($qrr)) {// para cada lexico exceto o alterado
                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                // verifica sinonimo[i] do lexico alterado no texto de cada lexico

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                    $qverif = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
                    echo("Query: " . $qverif . "<br>");
                    $resultado = mysql_query($qverif) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    if (!resultado) {
                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";
                        mysql_query($q) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
            }
        }
        # Verifica a ocorrencia dos sinonimos dos outros lexicos no lexico alterado

        $qSinonimos = "SELECT nome, id_lexico 
        		FROM sinonimo 
        		WHERE id_projeto = $id_projeto 
        		AND id_lexico <> $id_lexico 
        		AND id_pedidolex = 0";

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomeSinonimoEscapado = escapes_metacharacters($rowSinonimo["nome"]);
            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {

                // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                $qv = "SELECT * FROM lextolex where id_lexico_from=$id_lexico and id_lexico_to=" . $rowSinonimo['id_lexico'];
                $resultado = mysql_query($qv) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                if (!resultado) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES ($id_lexico, " . $rowSinonimo['id_lexico'] . ")";

                    mysql_query($q) or die("Erro ao enviar a query de insert(sinonimo) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }
        }

        # Cadastra os sinonimos novamente

        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($id_lexico, '" . prepares_data(strtolower($novoSin)) . "', $id_projeto)";

            mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        # Fim - cadastro de sinonimos        
    }

}

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
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

###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) {

    function removeRelacao($id_projeto, $id_relacao) {
        $DB = new PGDB ();

        $sql6 = new QUERY($DB);

        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }

}

###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################
function checarSinonimo($projeto, $listSinonimo) {
    $naoexiste = true;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    foreach ($listSinonimo as $sinonimo) {

        $q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }

        $q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        }
    }

    return $naoexiste;
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarLexico"))) {

    function inserirPedidoAdicionarLexico($id_projeto, $nome, $nocao, $impacto, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,'$nome','$nocao','$impacto','$classificacao',$id_usuario,'inserir',0)");

            $newId = $insere->getLastId();

            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");

            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");


            //insere sinonimos

            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
				VALUES ($newId, '" . prepares_data(strtolower($sin)) . "', $id_projeto)");
            }
            //fim da insercao dos sinonimos

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
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { //Eh gerente
            adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um lexico ela deve receber os campos do lexicos
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) {

    function inserirPedidoAlterarLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $justificativa, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($id_projeto,$id_lexico,'$nome','$nocao','$impacto',$id_usuario,'alterar',0,'$justificativa', '$classificacao')");

            $newPedidoId = $insere->getLastId();

            //sinonimos
            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
				VALUES ($newPedidoId,'" . prepares_data(strtolower($sin)) . "', $id_projeto)");
            }


            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");

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
                    mail("$mailGerente", "Pedido de Alterar L�xico", "O usuario do sistema $nome2\nPede para alterar o lexico $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { //Eh gerente
            alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) {

    function inserirPedidoRemoverLexico($id_projeto, $id_lexico, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);

        $qr = mysql_query("SELECT * FROM participa WHERE gerente = 1 
            AND id_usuario =" . _GET('$id_usuario') . " 
            AND id_projeto =" . _GET('$id_projeto')) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
       
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //nao e gerente

            $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico");
            $lexico = $select->gofirst();
            $nome = $lexico['nome'];

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)");
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
                    mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { // e gerente
            removeLexico($id_projeto, $id_lexico);
        }
    }

}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao"))) {

    function inserirPedidoRemoverRelacao($id_projeto, $id_relacao, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao");
        $relacao = $select->gofirst();
        $nome = $relacao['nome'];

        $insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)");
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
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}
?>

