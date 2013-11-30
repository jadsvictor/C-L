<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("simple_query"))) {

    function simple_query($field, $table, $where) {
        //test if the variable is not null
        assert($field != NULL);
        assert($table != NULL);
        assert($where != NULL);
                
        //test if a variable has the correct type
        assert(is_string($field));
        assert(is_string($table));
        assert(is_string($where));
        
        
        $connect_database = bd_connect() or die("Erro ao conectar ao SGBD");
        $select_field = "SELECT $field FROM $table WHERE $where";
        $result_select_field = mysql_query($select_field) or die("Erro ao enviar a query");
        $result_array = mysql_fetch_row($result_select_field);
        return $result_array[0];
    }

}
?>
