<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

###################################################################
# Checks whether a User and a project manager
# Gets the id of the project (1.1)
# Makes a select to get the resultArray "Participa" in table.(1.2)
# If result_array is not null: returned TRUE(1);(1.3)
# If result_array is null: returned FALSE(0);(1.4)
###################################################################

function verificaGerente($id_user, $id_project) {
    //test if a variable has the correct type
    assert(is_string($id_project));
    assert(is_string($id_user));

    //test if the variable is not null
    assert($id_project != NULL);
    assert($id_user != NULL);

    $ret = 0;
    $select = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario =" . (int) $_GET[$id_user] . "AND id_projeto =" . (int) $_GET[$id_project];
    $result_select = mysql_query($select) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $result_array = mysql_fetch_array($result_select);

    if ($result_array != false) {

        $ret = 1;
    } else {
        //nothing to do
    }
    return $ret;
}

?>
