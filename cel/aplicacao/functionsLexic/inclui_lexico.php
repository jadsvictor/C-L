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
        
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($r != NULL);
        
        $data = date("Y-m-d");
        //test if the variable is not null
        assert($data != NULL);


        $q = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" . prepares_data(strtolower($nome)) . "',
			  '" . prepares_data($nocao) . "', '" . prepares_data($impacto) . "', '$classificacao')";
        //test if the variable is not null
        assert($q != NULL);
        
        mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        //sinonimo
        $newLexId = mysql_insert_id($r);
        //test if the variable is not null
        assert($newLexId != NULL);


        if (!is_array($sinonimos))
            $sinonimos = array();

        foreach ($sinonimos as $novoSin) {
            $q = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($newLexId, '" . prepares_data(strtolower($novoSin)) . "', $id_projeto)";
            //test if the variable is not null
            assert($q != NULL);
            mysql_query($q, $r) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }

        $q = "SELECT max(id_lexico) FROM lexico";
        //test if the variable is not null
        assert($q != NULL);
        
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);
        
        $result = mysql_fetch_row($qrr);
        //test if the variable is not null
        assert($result!= NULL);
        
        return $result[0];
    }

}

?>
