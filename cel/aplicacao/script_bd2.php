<html>

    <head>
        <title></title>
    </head>

    <body>

        <?php
        include_once("bd.inc");
        include_once('auxiliar_bd.php');
        session_start();

        function converts_impacts() {
            $connect_database = bd_connect() or die("Erro na conexao ao BD : " . mysql_error() . __LINE__);

            $filename = "teste.txt";

            $query_selects_lexicon = "select * from lexico;";
            $result_selects_lexicon = mysql_query($query_selects_lexicon) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);

            if (!$handle = fopen($filename, 'w')) {
                print "Nao foi possivel abrir o arquivo !!!($filename)";
                exit;
            }

            while ($line = mysql_fetch_array($result_selects_lexicon, MYSQL_ASSOC)) {
                $id_lexicon = $line['id_lexico'];
                $impact = $line['impacto'];

                if (!fwrite($handle, "@\r\n$id_lexicon\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }

                if (!fwrite($handle, "$impact\r\n")) {
                    print "Cannot write to file ($filename)";
                    exit;
                }
            }

            fclose($handle);

            mysql_query("delete from impacto;");

            $lines = file($filename);

            $id_pick = "FALSE";
            $id_lexicon = 0;

            foreach ($lines as $line) {
                if ($line[0] == '@') {
                    $id_pick = 1;
                    continue;
                }
                if ($id_pick) {
                    $id = sscanf($line, "%d");
                    $id_lexicon = $id[0];
                    $id_pick = 0;
                    continue;
                }

                print ($line . "<br>\n");
                if (strcmp(trim($line), "") != 0) {
                    $query_insert_impact = "insert into impacto (id_lexico, impacto) values ('$id_lexicon', '$line');";
                    mysql_query($query_insert_impact) or die("A consulta ao BD falhou : " . mysql_error() . " " . $line . " " . $id_lexicon . " " . __LINE__);
                }
            }

            $query_selects_impact = "select * from impacto order by id_lexico;";
            $result_selects_impact = mysql_query($query_selects_impact) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
            mysql_num_rows($result_selects_impact);

            mysql_close($connect_database);
        }
        ?>

    </body>

</html>