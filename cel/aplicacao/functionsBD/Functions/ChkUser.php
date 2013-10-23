<?php

class ChkUser {

    function checkUserAuthentication($url) {

        if (isset($_SESSION["id_usuario_correntegit"])) {
            ?>
            <script language="javascript1.3">

                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');

            </script>

            <?php
            exit();
        }
    }

    funcTion simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $q = "SELECT $field FROM $table WHERE $where";
        $qrr = mysql_query($q) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}
?>
