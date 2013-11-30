<?php

include("functionsLexic/inclui_lexico.php");

// For correct inclusion of a term in the lexicon, a series of procedures
// Need to be taken (relating to requirement 'circular navigation'):
//
// 1. Including the new term in the database;
// 2. For all scenarios that project:
//      2.1. Search in: title, purpose, context, resources, actors, 
//      episodes for occurrences of the enclosed term or its synonyms;
//      2.2. For fields where occurrences are found:
//              2.2.1. Include table entry 'centolex';
// 3. For all the lexical terms that project (minus the newly inserted):
//      3.1. Browse notion, impact by occurrences of the word or its synonyms inserted;
//      3.2. For fields where occurrences are found:
//              3.2.1. Include entry in 'lextolex' table;
//      3.3. Browse notion, impact of term occurrences in terms entered by the lexicon 
//      of the same project;
//      3.4. If you find any occurrence:
//              3.4.1. Include entry in 'lextolex' table;

if (!(function_exists("adicionar_lexico"))) {

    function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao) {
        //test if the variable is not null
        assert($id_projeto != NULL);
        assert($nome != NULL);
        assert($nocao != NULL);
        assert($impacto != NULL);
        assert($sinonimos != NULL);
        assert($classificacao != NULL);
        
        //test if a variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($nome));
        assert(is_string($nocao));
        assert(is_string($impacto));
        assert(is_string($sinonimos));
        assert(is_string($classificacao));
        
        
        $connect_database = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)

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
                     VALUES (" . $result['id_cenario'] . ", $id_incluido)"; //2.2.1
                 //test if the variable is not null
                  assert($q != NULL);

                mysql_query($q) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }


        //sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {

            $qrr = mysql_query($qr) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($qrr)) {

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

                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                        (preg_match($regex, $result2['contexto']) != 0) ||
                        (preg_match($regex, $result2['atores']) != 0) ||
                        (preg_match($regex, $result2['recursos']) != 0) ||
                        (preg_match($regex, $result2['excecao']) != 0) ||
                        (preg_match($regex, $result2['episodios']) != 0)) {

                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    //test if the variable is not null
                    assert($qLex != NULL);
               
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                     //test if the variable is not null
                    assert($qrLex != NULL);
                    
                    $resultArraylex = mysql_fetch_array($qrLex);
                     //test if the variable is not null
                    assert($resultArraylex != NULL);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";

                         //test if the variable is not null
                        assert($q != NULL);
                        
                        mysql_query($q) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } 
                }               
            }            
        } 


        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico != $id_incluido";

         //test if the variable is not null
         assert($qlo != NULL);
                    
        //pega todos os outros lexicos
        $qrr = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
         //test if the variable is not null
         assert($qrr != NULL);
                    
        while ($result = mysql_fetch_array($qrr)) {    // (3)
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

                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                //test if the variable is not null
                assert($qLex != NULL);

                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                //test if the variable is not null
                assert($qrLex != NULL);
                
                $resultArraylex = mysql_fetch_array($qrLex);
                //test if the variable is not null
                assert($resultArraylex != NULL);

                if ($resultArraylex == false) {
                    $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";

                    //test if the variable is not null
                    assert($q != NULL);
                    mysql_query($q) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }

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
                    VALUES ($id_incluido, " . $result['id_lexico'] . ")";

                mysql_query($q) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }   // while
        //lexico para lexico

        $ql = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico != $id_incluido";
       
        //test if the variable is not null
        assert($ql != NULL);
        
        //sinonimos dos outros lexicos no texto do inserido
        
        $qrr = mysql_query($ql) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);
        
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) {
            while ($resultl = mysql_fetch_array($qrr)) {

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

                if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0)) {

                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    //test if the variable is not null
                    assert($qLex != NULL);
                    
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    //test if the variable is not null
                    assert($qrLex != NULL);
                
                    $resultArraylex = mysql_fetch_array($qrLex);
                    //test if the variable is not null
                    assert($resultArraylex != NULL);

                    if ($resultArraylex == false) {

                        $q = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                         VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";
                        //test if the variable is not null
                        assert($q != NULL);
                
                        mysql_query($q) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                                        
                    }
                }   
            }
        }
        //sinonimos ja existentes

        $qSinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";
        //test if the variable is not null
        assert($qSinonimos != NULL);
        
        $qrrSinonimos = mysql_query($qSinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrrSinonimos != NULL);
        
        $nomesSinonimos = array();

        $id_lexicoSinonimo = array();

        while ($rowSinonimo = mysql_fetch_array($qrrSinonimos)) {

            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }

}
?>
