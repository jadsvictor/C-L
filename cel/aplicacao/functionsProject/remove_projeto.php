<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################
function removeProjeto($id_projeto) {
    //test if the variable is not null
    assert($id_projeto != NULL);
    //test if a variable has the correct type
    assert(is_string($id_projeto));
    
    $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de cenario
    $deletaPedidoCenario = "Delete FROM pedidocen WHERE id_projeto = '$id_projeto' ";
        //test if the variable is not null
        assert($deletaPedidoCenario != NULL);
    mysql_query($deletaPedidoCenario) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os pedidos de lexico
    $deletaPedidoLexico = "Delete FROM pedidolex WHERE id_projeto = '$id_projeto' ";
        //test if the variable is not null
        assert($deletaPedidoLexico != NULL);
    mysql_query($deletaPedidoLexico) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //Remove os lexicos //verificar lextolex!!!
    $deletaLex = "SELECT * FROM lexico WHERE id_projeto = '$id_projeto' ";
        //test if the variable is not null
        assert($deletaLex != NULL);
    $resultadoDeletaLexico = mysql_query($deletaLex) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($resultadoDeletaLexico != NULL);
        
    while ($result = mysql_fetch_array($resultadoDeletaLexico)) {
        $id_lexico = $result['id_lexico']; //seleciona um lexico

        $deletaLexToLex = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
            //test if the variable is not null
            assert($deletaLexToLex != NULL);
        mysql_query($deletaLexToLex) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $deletaCenToLex = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
            //test if the variable is not null
            assert($deletaCenToLex != NULL);
        mysql_query($deletaCenToLex) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $deletaSinonimo = "Delete FROM sinonimo WHERE id_projeto = '$id_projeto'";
           //test if the variable is not null
            assert($deletaSinonimo != NULL);
        mysql_query($deletaSinonimo) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $deletaLexico = "Delete FROM lexico WHERE id_projeto = '$id_projeto' ";
        //test if the variable is not null
        assert($deletaLexico != NULL);
    mysql_query($deletaLexico) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remove os cenarios
    $removeCenario = "SELECT * FROM cenario WHERE id_projeto = '$id_projeto' ";
            //test if the variable is not null
            assert($removeCenario != NULL);
    $resultRemoveCenario = mysql_query($removeCenario) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            //test if the variable is not null
            assert($resultRemoveCenario != NULL);
  
    while ($resultArrayCenario = mysql_fetch_array($resultRemoveCenario)) {
        //test if the variable is not null
        assert($resultArrayCenario != NULL);
            
        $id_lexico = $resultArrayCenario['id_cenario']; //seleciona um lexico

        $deletaCentoCen = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
            //test if the variable is not null
            assert($deletaCentoCen != NULL);
        mysql_query($deletaCentoCen) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        $deletaLextoLex = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
            //test if the variable is not null
            assert($deletaLextoLex != NULL);
        mysql_query($deletaLextoLex) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    }

    $deletaLexico  = "Delete FROM cenario WHERE id_projeto = '$id_projeto' ";
            //test if the variable is not null
            assert($deletaLexico != NULL);
    mysql_query($deletaLexico) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover participantes
    $deletaParticipantes = "Delete FROM participa WHERE id_projeto = '$id_projeto' ";
            //test if the variable is not null
            assert($deletaParticipantes != NULL);
    mysql_query($deletaParticipantes) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover publicacao
    $deletaPublicacao = "Delete FROM publicacao WHERE id_projeto = '$id_projeto' ";
            //test if the variable is not null
            assert($deletaPublicacao != NULL);
    mysql_query($deletaPublicacao) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

    //remover projeto
    $deletaProjeto = "Delete FROM projeto WHERE id_projeto = '$id_projeto' ";
            //test if the variable is not null
            assert($deletaProjeto != NULL);
    mysql_query($deletaProjeto) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}

?>
