<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################

function verificaGerente($id_usuario, $id_projeto) {
     //test if a variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_usuario));
        
        //test if the variable is not null
        assert($id_projeto != NULL);
        assert($id_usuario != NULL);
    
    $ret = 0;
    $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario =" .  (int)$_GET[$id_usuario] .  "AND id_projeto ="  .  (int)$_GET[$id_projeto];
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {

        $ret = 1;
    }
    return $ret;
}

?>
