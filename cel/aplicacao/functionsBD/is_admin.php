<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

// Retorna TRUE ssse $id_usuario eh admin de $id_projeto
if (!(function_exists("is_admin"))) {

    function is_admin($id_user, $id_project) {

        //test if a variable has the correct type
        assert(is_string($id_project));
        assert(is_string($id_user));

        //test if the variable is not null
        assert($id_project != NULL);
        assert($id_user != NULL);

        $connect_database = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $select = "SELECT * FROM participa WHERE id_usuario =" . (int) $_GET[$id_user] . "
            AND id_projeto = " . (int) $_GET[$id_project] . "
              AND gerente = 1";
        $result_select = mysql_query($select) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($result_select));
    }

} else {
    //nothing to do
}
?>
