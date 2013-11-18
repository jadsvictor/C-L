<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

###################################################################
# Insere um projeto no banco de dados.
# Recebe o nome e descricao. (1.1)
# Verifica se este usuario ja possui um projeto com esse nome. (1.2)
# Caso nao possua, insere os valores na tabela PROJETO. (1.3)
# Devolve o id_cprojeto. (1.4)
#
###################################################################

    function inclui_projeto($nome, $descricao) {
        //test if the variable is not null
        assert($nome != NULL);
        assert($descricao != NULL);
        
        //test if a variable has the correct type
        assert(is_string($nome));
        assert(is_string($descricao));
        
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //verifica se usuario ja existe
        $qrv = mysql_query("SELECT * FROM users WHERE name = '" . mysql_real_escape_string($_GET["nome"]) . "'");
        //test if the variable is not null
        assert($qrv != NULL);
        
        $resultArray = mysql_fetch_array($qvr);
        //test if the variable is not null
        assert($resultArray != NULL);


        if ($resultArray != false) {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];
            //test if the variable is not null
            assert($id_projeto_repetido != NULL);
            //test if a variable has the correct type
            assert(is_string($id_projeto_repetido));

            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
            //test if the variable is not null
            assert($id_usuario_corrente != NULL);
            //test if a variable has the correct type
            assert(is_string($id_usuario_corrente));

            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' 
                AND id_usuario = '$id_usuario_corrente' ";
            //test if the variable is not null
            assert($qvu != NULL);
       
            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            //test if the variable is not null
            assert($qvuv != NULL);
            
            $resultArray = mysql_fetch_row($qvuv);
            //test if the variable is not null
            assert($resultArray != NULL);

            if ($resultArray[0] != null) {
                return -1;
            }
        }

        $q = "SELECT MAX(id_projeto) FROM projeto";
        //test if the variable is not null
        assert($q != NULL);
        
        $qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //test if the variable is not null
        assert($qrr != NULL);
        
        $result = mysql_fetch_row($qrr);
        //test if the variable is not null
        assert($result != NULL);

        if ($result[0] == false) {
            $result[0] = 1;
        } else {
            $result[0]++;
        }
        
        $data = date("Y-m-d");
        //test if the variable is not null
        assert($data != NULL);
        
        $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . prepares_data($nome) . "','$data' , '" . prepares_data($descricao) . "')";
        //test if the variable is not null
        assert($qr != NULL);
            
        mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        return $result[0];
    }

?>
