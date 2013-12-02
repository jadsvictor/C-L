<?php

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################

function checarLexicoExistente($projeto, $nome) {
    //test if the variable is not null
    assert($projeto != NULL);
    assert($nome != NULL);
    //test if a variable has the correct type
    assert(is_string($projeto));
    assert(is_string($nome));

    $naoexiste = false;

    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $q = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$nome' ";
    //test if the variable is not null
    assert($q != NULL);
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    //test if the variable is not null
    assert($qr != NULL);
    $resultArray = mysql_fetch_array($qr);
    if ($resultArray == false) {
        $naoexiste = true;
    } else {
        //nothing to do
    }

    $q = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$nome' ";
    //test if the variable is not null
    assert($q != NULL);
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    //test if the variable is not null
    assert($qr != NULL);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {
        $naoexiste = false;
    } else {
        //nothing to do
    }

    return $naoexiste;
}

?>
