<?php
include_once 'estruturas.php';
include_once 'auxiliar_algoritmo.php';
include_once 'bd.inc';

session_start();

function get_lista_de_sujeito() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'sujeito' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_objeto() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'objeto' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_verbo() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'verbo' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

function get_lista_de_estado() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from lexico where tipo = 'estado' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = obter_termo_do_lexico($line);
    }
    sort($aux);
    return $aux;
}

// This function checks whether all members of the table has a type defined lexicons
// If there are no records in the table define type, the function returns these records
// Otherwise returns true
function verifica_tipo() {
    $id_projeto = $_SESSION['id_projeto'];
    $query = "select * from lexico where tipo is null AND id_projeto='$id_projeto' order by id_lexico;";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $result2 = mysql_num_rows($result);
    $col_value = $result2;
    if ($col_value > 0) {
        $aux = array();
        while ($line2 = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $aux[] = $line2['id_lexico'];
        }
        mysql_free_result($result);
        return($aux);
    }
    
     else {
        mysql_free_result($result);
        return(TRUE);
    }
}

// This function updates the type of lexical $ id_lexico (integer) for $ type (string)
// This function only accepts types: subject, object, verb, state and NULL
function atualiza_tipo($id_lexico, $tipo) {
    $id_projeto = $_SESSION['id_projeto'];
    if (!(($tipo != "sujeito") || ($tipo != "objeto") || ($tipo != "verbo") || ($tipo != "estado") || ($tipo != "null"))) {
        return (FALSE);
    }
    
    else{
         //nothing to do
    }
   
    mysql_query("update lexico set tipo = '" .  mysql_real_escape_string($_GET["tipo"]) . "'" . 
            " where id_lexico =" .  (int)$_GET["id_lexico"]) or die("A consulta � BD falhou : " 
            . mysql_error() . __LINE__);
    return(TRUE);
}

function obter_lexico($id_lexico) {
    $id_projeto = $_SESSION['id_projeto'];
     // returns all fields of the lexicon, each field is a position of the array that
���� // can be indexed by field name or by an integer index.
    $query = "select * from lexico where id_lexico = '$id_lexico' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    return($line);
}

function obter_termo_do_lexico($lexico) {
    $id_projeto = $_SESSION['id_projeto'];
    $impactos = array();
    $id_lexico = $lexico['id_lexico'];
    $query = "select impacto from impacto where id_lexico = '$id_lexico'";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $impactos[] = strtolower($line['impacto']);
    }
    $termo_do_lexico = new termo_do_lexico(strtolower($lexico['nome']), strtolower($lexico['nocao']), $impactos);
    return $termo_do_lexico;
}

function cadastra_impacto($id_lexico, $impacto) {
    $id_projeto = $_SESSION['id_projeto'];
    $query = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$impacto');";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "select * from impacto where impacto = '$impacto' and id_lexico = $id_lexico;";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_ASSOC);
    $id_impacto = $line['id_impacto'];
    return $id_impacto;
}

//criar tabela para conceitos (class conceito)
function get_lista_de_conceitos() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select * from conceito where id_projeto='$id_projeto';";
    $result1 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result1, MYSQL_BOTH)) {
        $conc = new conceito($line['nome'], $line['descricao']);
        $conc->namespace = $line['namespace'];
        $id = $line['id_conceito'];
        $query = "select * from relacao_conceito where id_conceito = '$id' AND id_projeto='$id_projeto';";
        $result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        while ($line2 = mysql_fetch_array($result2, MYSQL_BOTH)) {
            $idrel = $line2['id_relacao'];
            $query = "select * from relacao where id_relacao = '$idrel' AND id_projeto='$id_projeto';";
            $result3 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
            $line3 = mysql_fetch_array($result3, MYSQL_BOTH);
            $rel = $line3['nome'];
            $pred = $line2['predicado'];
            $indice = existe_relacao($rel, $conc->relacoes);
            if ($indice != -1) {
                $conc->relacoes[$indice]->predicados[] = $pred;
            } else {
                $conc->relacoes[] = new relacao_entre_conceitos($pred, $rel);
            }
        }
        $aux[] = $conc;
    }
    sort($aux);
    $query = "select * from hierarquia where id_projeto='$id_projeto';";
    $result1 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result1, MYSQL_BOTH)) {

        $id_conceito = $line['id_conceito'];
        $query = "select * from conceito where id_conceito = '$id_conceito' AND id_projeto='$id_projeto';";
        $result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        $line2 = mysql_fetch_array($result2, MYSQL_BOTH);
        $conceito_nome = $line2['nome'];
        $id_subconceito = $line['id_subconceito'];
        $query = "select * from conceito where id_conceito = '$id_subconceito' AND id_projeto='$id_projeto';";
        $result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        $line2 = mysql_fetch_array($result2, MYSQL_BOTH);
        $subconceito_nome = $line2['nome'];
        foreach ($aux as $key => $conc1) {
            if ($conc1->nome == $conceito_nome) {
                $aux[$key]->subconceitos[] = $subconceito_nome;
            }
            
            else{
               //nothing to do
            }
            
        }
    }
    return $aux;
}

// create table to concepts (class relacao_entre_conceitos)
function get_lista_de_relacoes() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select nome from relacao where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = $line['nome'];
    }
    sort($aux);
    return $aux;
}

//criar tabela para axiomas (string)
function get_lista_de_axiomas() {
    $id_projeto = $_SESSION['id_projeto'];
    $aux = array();
    $query = "select axioma from axioma where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $aux[] = $line['axioma'];
    }
    sort($aux);
    return $aux;
}

function get_funcao() {
    $id_projeto = $_SESSION['id_projeto'];
    $query = "select valor from algoritmo where nome = 'funcao' AND id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $line = mysql_fetch_array($result, MYSQL_BOTH);
    return $line['valor'];
}

function get_indices() {
    $id_projeto = $_SESSION['id_projeto'];
    $query = "select * from algoritmo where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $indice = array();
    while ($line = mysql_fetch_array($result, MYSQL_BOTH)) {
        $indice[$line['nome']] = $line['valor'];
    }
    return $indice;
}

function salvar_algoritmo() {
    $id_projeto = $_SESSION['id_projeto'];
    $link = bd_connect();
    foreach ($_SESSION["lista_de_conceitos"] as $conceit) {
        print($conceit->nome);
        foreach ($conceit->relacoes as $rel) {
            print("<br>----$rel->verbo");
            foreach ($rel->predicados as $pred) {
                print("<br>--------$pred");
            }
        }
    }
    $query = "delete from relacao where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from conceito where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from relacao_conceito where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from axioma where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from algoritmo where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    $query = "delete from hierarquia where id_projeto='$id_projeto';";
    $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);

    if (isset($_SESSION["lista_de_relacoes"])) {
        foreach ($_SESSION["lista_de_relacoes"] as $relacao) {
            $query = "insert into relacao (nome, id_projeto) values ('$relacao', '$id_projeto');";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        }
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["lista_de_conceitos"])) {
        foreach ($_SESSION["lista_de_conceitos"] as $conc) {
            $query = "select id_conceito from conceito where nome = 
                      '$conc->nome' and id_projeto='$id_projeto';";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
            $id_conceito = 0;
            if (mysql_num_rows($result) > 0) {
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_conceito = $line['id_conceito'];
            } 
            
            else {
                $query = "insert into conceito (nome,descricao,namespace, id_projeto)
                          values ('$conc->nome', '$conc->descricao','$conc->namespace' ,'$id_projeto');";
                $result = mysql_query($query) or die("A consulta � BD falhou : " .
                                mysql_error() . __LINE__);
                $query = "select id_conceito from conceito where 
                         nome = '$conc->nome' and id_projeto='$id_projeto';";
                $result = mysql_query($query) or die("A consulta � BD falhou : "
                                . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_conceito = $line['id_conceito'];
            }
            
            foreach ($conc->relacoes as $relacao) {
                $verbo = $relacao->verbo;
                $query = "select id_relacao from relacao where nome = '$verbo' 
                         and id_projeto='$id_projeto';";
                $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                $line = mysql_fetch_array($result, MYSQL_BOTH);
                $id_relacao = $line['id_relacao'];
                $predicados = $relacao->predicados;
                foreach ($predicados as $pred) {
                    $query = "insert into relacao_conceito (id_conceito,
                              id_relacao,predicado,id_projeto)
                              values ('$id_conceito', '$id_relacao', '$pred', '$id_projeto');";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " .
                                    mysql_error() . __LINE__);
                }
            }
        }
        foreach ($_SESSION["lista_de_conceitos"] as $conc) {
            foreach ($conc->subconceitos as $subconceito) {
                if ($subconceito != -1) {
                    $query = "select id_conceito from conceito where nome = 
                             '$subconceito' and id_projeto='$id_projeto';";
                    $result = mysql_query($query) or die("A consulta � BD falhou : "
                                    . mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_subconceito = $line['id_conceito'];
                    $nome = $conc->nome;
                    $query = "select id_conceito from conceito where nome = '$nome' 
                              and id_projeto='$id_projeto';";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " .
                                    mysql_error() . __LINE__);
                    $line = mysql_fetch_array($result, MYSQL_BOTH);
                    $id_conceito = $line['id_conceito'];

                    $query = "insert into hierarquia (id_conceito,id_subconceito,id_projeto)
                              values ('$id_conceito', '$id_subconceito','$id_projeto');";
                    $result = mysql_query($query) or die("A consulta � BD falhou : " .
                                    mysql_error() . __LINE__);
                }
                
                 else{
                        //nothing to do
                }
            }
        }
    }
    
     else{
         //nothing to do
    }
    
    if (isset($_SESSION["lista_de_axiomas"])) {
        foreach ($_SESSION["lista_de_axiomas"] as $axioma) {
            $query = "insert into axioma (axioma,id_projeto) values ( '$axioma','$id_projeto' );";
            $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
        }
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["funcao"])) {
        $func = $_SESSION['funcao'];
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('funcao',";
        $query = $query . "'" . $func . "', '$id_projeto' );";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["index1"])) {
        $query = "insert into algoritmo (nome, valor,id_projeto) values ('index1',";
        $query = $query . "'" . $_SESSION['index1'] . "', '$id_projeto');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["index3"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index3',";
        $query = $query . "'" . $_SESSION['index3'] . "', '$id_projeto');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["index4"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index4',";
        $query = $query . "'" . $_SESSION['index4'] . "', '$id_projeto');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    
    else{
         //nothing to do
    }
    
    if (isset($_SESSION["index5"])) {
        $query = "insert into algoritmo (nome, valor, id_projeto) values ('index5',";
        $query = $query . "'" . $_SESSION['index5'] . "', '$id_projeto');";
        $result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
    }
    
    else{
         //nothing to do
    }
    
    mysql_close($link);

    if ($_SESSION["funcao"] != 'fim') {
        ?>
        <script>
            document.location = "auxiliar_interface.php";
        </script>
        <?php
    } 
    
    else {
        ?>
        <script>
            document.location = "algoritmo.php";
        </script>
        <?php
    }
}

if (isset($_SESSION["tipos"])) {
    session_unregister("tipos");

    include_once 'bd.inc';

    $connect_database = bd_connect();

    $list = verifica_tipo();

    foreach ($list as $key => $termo) {
        $aux = $_POST["type" . $key];
        echo ("$termo, $aux <br>");
        if (!atualiza_tipo($termo, $aux)) {
            echo "ERRO <br>";
        }
    }
    mysql_close($connect_database);
    ?>
    <script>
        document.location = "algoritmo_inicio.php";
    </script>
    <?php
}

else{
     //nothing to do
}
    
if (array_key_exists("save", $_POST)) {
    salvar_algoritmo();
}

else{
    //nothing to do
}
?>

<html>
    <head>
        <title>Auxiliar BD</title>
        <style>

        </style>
    </head>
    <body>
    </body>
</html>