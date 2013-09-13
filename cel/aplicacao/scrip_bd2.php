<html> 

    <head> 
        <title></title> 
    </head> 

    <body> 

        <?php
        include 'auxiliar_bd.php';
        include_once("bd.inc");
        include_once("CELConfig/CELConfig.inc");

        $conecta_banco = bd_connect() or die("Erro na conexao ao BD : " . mysql_error() . __LINE__);

        if ($conecta_banco && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXAO AO BD <br>";
        else
            echo "ERRO NA CONEXAO AO BD <br>";

        $query_altera_conceito = "alter table conceito add namespace varchar(250) NULL after descricao;";
        $result_altera_conceito = mysql_query($query_altera_conceito) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);


        echo "<br>FIM !!!";


        mysql_close($conecta_banco);
        ?> 

    </body> 

</html> 
