<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

// Retorna TRUE se $id_usuario tem permissao sobre $id_projeto
if (!(function_exists("check_proj_perm"))) {

    function check_proj_perm($id_usuario, $id_projeto) {
        //test if the variable is not null
        assert($id_usuario != NULL);
        assert($id_projeto != NULL);
        //test if a variable has the correct type
        assert(is_string($id_projeto));
        assert(is_string($id_usuario));
            
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT * FROM participa WHERE id_usuario =" .  (int)$_GET[$id_usuario] .  "AND id_projeto ="  .  (int)$_GET[$id_projeto];
        //test if the variable is not null
        assert($q != NULL);
        
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);
        
        return (1 == mysql_num_rows($qrr));
    }

}

?>
