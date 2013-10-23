<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");


/* chkUser(): checa se o usu�rio acessando foi autenticado (presen�a da vari�vel de sess�o
  $id_usuario_corrente). Caso ele j� tenha sido autenticado, continua-se com a execu��o do
  script. Caso contr�rio, abre-se uma janela de logon. */
if (!(function_exists("checkUserAuthentication"))) {

    function checkUserAuthentication($url) {

        if( isset($_SESSION["id_usuario_correntegit"]))  {
           
            ?>
            <script language="javascript1.3">
                
                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
  
            </script>

            <?php
            exit();
        }
    }

}

if (!(function_exists("simple_query"))) {

    funcTion simple_query($field, $table, $where) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD");
        $q = "SELECT $field FROM $table WHERE $where";
        $qrr = mysql_query($q) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($qrr);
        return $result[0];
    }

}

#############################################
#Deprecated by the author:
#Essa funcao deveria receber um id_projeto
#de forma a verificar se o gerente pertence
#a esse projeto.Ela so verifica atualmente
#se a pessoa e um gerente.
#############################################
if (!(function_exists("verificaGerente"))) {

    function verificaGerente($id_usuario) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $select->execute("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario");
        if ($select->getntuples() == 0) {
            return 0;
        } else {
            return 1;
        }
    }

}

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {

    function formataData($data) {

        $novaData = substr($data, 8, 9) .
                substr($data, 4, 4) .
                substr($data, 0, 4);
        return $novaData;
    }

}





// Retorna TRUE ssse $id_usuario eh admin de $id_projeto
if (!(function_exists("is_admin"))) {

    function is_admin($id_usuario, $id_projeto) {
        $r = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $q = "SELECT * FROM participa WHERE id_usuario =" . (int)$_GET[$id_usuario] . "
            AND id_projeto = " . (int)$_GET[$id_projeto] . "
              AND gerente = 1";
        $qrr = mysql_query($q) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($qrr));
    }

}


###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################

function verificaGerente($id_usuario, $id_projeto) {
    $ret = 0;
    $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario =" .  (int)$_GET[$id_usuario] .  "AND id_projeto ="  .  (int)$_GET[$id_projeto];
    $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($qr);

    if ($resultArray != false) {

        $ret = 1;
    }
    return $ret;
}


