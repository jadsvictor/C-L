<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

// Retorna TRUE ssse $id_usuario eh admin de $id_projeto
if (!(function_exists("is_admin"))) {

    function is_admin($id_usuario, $id_projeto) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT * FROM participa WHERE id_usuario =" . (int)$_GET[$id_usuario] . "
            AND id_projeto = " . (int)$_GET[$id_projeto] . "
              AND gerente = 1";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($qrr));
    }

}

?>
