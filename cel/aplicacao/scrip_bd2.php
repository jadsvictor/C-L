<html> 

    <head> 
        <title></title> 
    </head> 

    <body> 

        <?php
        include_once("bd.inc");
        include_once("CELConfig/CELConfig.inc");

        $connect_database = bd_connect() or die("Erro na conexao ao BD : " . mysql_error() . __LINE__);

        if ($connect_database && mysql_select_db(CELConfig_ReadVar("BD_database")))
            echo "SUCESSO NA CONEXAO AO BD <br>";
        else
            echo "ERRO NA CONEXAO AO BD <br>";

        $query_concept_changes = "alter table conceito add namespace varchar(250) NULL after descricao;";
        mysql_query($query_concept_changes) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);


        echo "<br>FIM !!!";


        mysql_close($connect_database);
        ?> 

    </body> 

</html> 
