<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php
        include_once("bd.inc");
        include_once('auxiliar_bd.php');
        session_start();

        function converte_impactos() {
            $conecta_banco = bd_connect() or die("Erro na conexao ao BD : " . mysql_error() . __LINE__);

            $filename = "teste.txt";

            $query_seleciona_lexico = "select * from lexico;";
            $result_seleciona_lexico = mysql_query($query_seleciona_lexico) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

            if (!$handle = fopen($filename, 'w')) {
                print "Nao foi possivel abrir o arquivo !!!($filename)";
                exit;
            }

            while ($line = mysql_fetch_array($result_seleciona_lexico, MYSQL_ASSOC)) {
                $id_lexico = $line['id_lexico'];
                $impacto = $line['impacto'];

                if (!fwrite($handle, "@\r\n$id_lexico\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }

                if (!fwrite($handle, "$impacto\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }
            }

            fclose($handle);

            mysql_query("delete from impacto;");

            $lines = file($filename);

            $pegar_id = "FALSE";
            $id_lexico = 0;

            foreach ($lines as $line_num => $line) {
                if ($line[0] == '@') {
                    $pegar_id = 1;
                    continue;
                }
                if ($pegar_id) {
                    $id = sscanf($line, "%d");
                    $id_lexico = $id[0];
                    $pegar_id = 0;
                    continue;
                }

                print ($line . "<br>\n");
                if (strcmp(trim($line), "") != 0) {
                    $query_inserir_impacto = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$line');";
                    mysql_query($query_inserir_impacto) or die("A consulta ao BD falhou : " . mysql_error() . " " . $line . " " . $id_lexico . " " . __LINE__);
                }
            }

            $query_seleciona_impacto = "select * from impacto order by id_lexico;";
            $result_seleciona_impacto = mysql_query($query_seleciona_impacto) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
            mysql_num_rows($result_seleciona_impacto);

            mysql_close($conecta_banco);
        }
        ?>

    </body>

</html>