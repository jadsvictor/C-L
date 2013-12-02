<?php

###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("alteraLexico"))) {

    function alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        //test if the variable is not null
        assert($id_projeto != NULL);
        assert($id_lexico != NULL);
        assert($nome != NULL);
        assert($nocao != NULL);
        assert($impacto != NULL);
        assert($sinonimos != NULL);
        assert($classificacao != NULL);

        //test if a variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_lexico));
        assert(is_string($nome));
        assert(is_string($nocao));
        assert(is_string($impacto));
        assert(is_string($sinonimos));
        assert(is_string($classificacao));

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
        //test if the variable is not null
        assert($qr != NULL);

        $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);

        while ($result = mysql_fetch_array($qrr)) {    // 2  - Para todos os cenarios
            $nomeEscapado = escapes_metacharacters($nome);
            //test if the variable is not null
            assert($nomeEscapado != NULL);
            //test if a variable has the correct type
            assert(is_string($nomeEscapado));

            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            //test if the variable is not null
            assert($regex != NULL);
            //test if a variable has the correct type
            assert(is_string($regex));

            if ((preg_match($regex, $result['objetivo']) != 0) ||
                    (preg_match($regex, $result['contexto']) != 0) ||
                    (preg_match($regex, $result['atores']) != 0) ||
                    (preg_match($regex, $result['recursos']) != 0) ||
                    (preg_match($regex, $result['excecao']) != 0) ||
                    (preg_match($regex, $result['episodios']) != 0)) { //2.2
                $q = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_lexico)"; //2.2.1
                //test if the variable is not null
                assert($q != NULL);

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            } else {
                //nothing to do
            }
        }
        # Fim da verificacao
        # Verifica se h� alguma ocorrencia de algum dos sinonimos do lexico nos cenarios existentes no banco
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {//Para cada sinonimo
            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            //test if the variable is not null
            assert($qrr != NULL);

            while ($result2 = mysql_fetch_array($qrr)) {// para cada cenario
                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                //test if the variable is not null
                assert($nomeSinonimoEscapado != NULL);
                //test if a variable has the correct type
                assert(is_string($nomeSinonimoEscapado));

                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                //test if the variable is not null
                assert($regex != NULL);
                //test if a variable has the correct type
                assert(is_string($regex));
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
        //test if the variable is not null
        assert($qlo != NULL);

        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);

        while ($result = mysql_fetch_array($qrr)) { // para cada lexico exceto o que esta sendo alterado    // (3)
            # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
            $nomeEscapado = escapes_metacharacters($nome);
            //test if the variable is not null
            assert($nomeEscapado != NULL);
            //test if a variable has the correct type
            assert(is_string($nomeEscapado));

            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            //test if the variable is not null
            assert($regex != NULL);
            //test if a variable has the correct type
            assert(is_string($regex));

            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                    (preg_match($regex, $result['impacto']) != 0)) {
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                      	VALUES (" . $result['id_lexico'] . ", $id_lexico)";
                //test if the variable is not null
                assert($q != NULL);

                mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            } else {
                //nothing to do
            }

            # Verifica a ocorrencia do titulo dos outros lexicos no texto do lexico alterado

            $nomeEscapado = escapes_metacharacters($result['nome']);
            //test if the variable is not null
            assert($nomeEscapado != NULL);
            //test if a variable has the correct type
            assert(is_string($nomeEscapado));

            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            //test if the variable is not null
            assert($regex != NULL);
            //test if a variable has the correct type
            assert(is_string($regex));

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {   // (3.3)        
                $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                		VALUES ($id_lexico, " . $result['id_lexico'] . ")";
                //test if the variable is not null
                assert($q != NULL);

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            } else {
                //nothing to do
            }
        }
        # Fim da verificao por titulo

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico <> $id_lexico";
        //test if the variable is not null
        assert($ql != NULL);

        # Verifica a ocorrencia dos sinonimos do lexico alterado nos outros lexicos

        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {// para cada sinonimo do lexico alterado
            $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            //test if the variable is not null
            assert($qrr != NULL);

            while ($resultl = mysql_fetch_array($qrr)) {// para cada lexico exceto o alterado
                $nomeSinonimoEscapado = escapes_metacharacters($sinonimos[$i]);
                //test if the variable is not null
                assert($nomeSinonimoEscapado != NULL);
                //test if a variable has the correct type
                assert(is_string($nomeSinonimoEscapado));

                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                //test if the variable is not null
                assert($regex != NULL);
                //test if a variable has the correct type
                assert(is_string($regex));

                // verifica sinonimo[i] do lexico alterado no texto de cada lexico

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                    $qverif = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
                    //test if the variable is not null
                    assert($qverif != NULL);

                    echo("Query: " . $qverif . "<br>");
                    $resultado = mysql_query($qverif) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    //test if the variable is not null
                    assert($resultado != NULL);

                    if (!resultado) {
                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";
                        //test if the variable is not null
                        assert($q != NULL);

                        mysql_query($q) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } else {
                        //nothing to do
                    }
                } else {
                    //nothing to do
                }
            }
        }
        # Verifica a ocorrencia dos sinonimos dos outros lexicos no lexico alterado

        $qSinonimos = "SELECT nome, id_lexico 
        		FROM sinonimo 
        		WHERE id_projeto = $id_projeto 
        		AND id_lexico <> $id_lexico 
        		AND id_pedidolex = 0";
        //test if the variable is not null
        assert($qSinonimos != NULL);

        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrrSinonimos != NULL);

        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {
            $nomeSinonimoEscapado = escapes_metacharacters($rowSinonimo["nome"]);
            //test if the variable is not null
            assert($nomeSinonimoEscapado != NULL);
            //test if a variable has the correct type
            assert(is_string($nomeSinonimoEscapado));

            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
            //test if the variable is not null
            assert($regex != NULL);
            //test if a variable has the correct type
            assert(is_string($regex));

            if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0)) {

                // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                $qv = "SELECT * FROM lextolex where id_lexico_from=$id_lexico and id_lexico_to=" . $rowSinonimo['id_lexico'];
                //test if the variable is not null
                assert($qv != NULL);

                $resultado = mysql_query($qv) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                //test if the variable is not null
                assert($resultado != NULL);

                if (!resultado) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES ($id_lexico, " . $rowSinonimo['id_lexico'] . ")";
                    //test if the variable is not null
                    assert($q != NULL);

                    mysql_query($q) or die("Erro ao enviar a query de insert(sinonimo) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                } else {
                    // nothing to do
                }
            } else {
                // nothing to do
            }

            # Cadastra os sinonimos novamente

            if (!is_array($sinonimos)) {
                $sinonimos = array();
            } else {
                //nothing to do
            }

            foreach ($sinonimos as $novoSin) {
                $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($id_lexico, '" . prepares_data(strtolower($novoSin)) . "', $id_projeto)";
                //test if the variable is not null
                assert($q != NULL);

                mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }

            # Fim - cadastro de sinonimos        
        }
    }

} else {
    //nothing to do
}
?>
