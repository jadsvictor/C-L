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

?>
