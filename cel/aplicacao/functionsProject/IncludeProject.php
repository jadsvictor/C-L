<?php

class IncludeProject {

    function inclui_projeto($nome, $descricao) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //verifica se usuario ja existe
        $qrv = mysql_query("SELECT * FROM users WHERE name = '" . mysql_real_escape_string($_GET["name"]) . "'");

        //$result = mysql_fetch_row($qvr);
        $resultArray = mysql_fetch_array($qvr);


        if ($resultArray != false) {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];

            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];

            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";

            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            $resultArray = mysql_fetch_row($qvuv);

            if ($resultArray[0] != null) {
                return -1;
            }
        }

        $q = "SELECT MAX(id_projeto) FROM projeto";
        $qrr = mysql_query($q) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($qrr);

        if ($result[0] == false) {
            $result[0] = 1;
        } else {
            $result[0]++;
        }
        $data = date("Y-m-d");

        $qr = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'" . prepares_data($nome) . "','$data' , '" . prepares_data($descricao) . "')";

        mysql_query($qr) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        return $result[0];
    }

}

?>
