<?php

###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################

function checarSinonimo($projeto, $listSinonimo) {
    //test if the variable is not null
    assert($projeto != NULL);
    assert($listSinonimo != NULL);
    //test if a variable has the correct type
    assert(is_string($projeto));
    assert(is_string($listSinonimo));

    $naoexiste = true;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    foreach ($listSinonimo as $sinonimo) {

        $q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        //test if the variable is not null
        assert($q != NULL);

        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qr != NULL);

        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        } else {
            //nothing to do
        }

        $q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        //test if the variable is not null
        assert($q != NULL);

        $qr = mysql_query($q) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qr != NULL);

        $resultArray = mysql_fetch_array($qr);
        if ($resultArray != false) {
            $naoexiste = false;
            return $naoexiste;
        } else {
            //nothing to do
        }
    }

    return $naoexiste;
}

?>
