<HTML> 
    <HEAD> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <TITLE>Recupera��o de Arquivos DAML</TITLE> 
    </HEAD> 

    <BODY> 
        <H2>Hist�rico de Arquivos DAML</H2> 
        <?PHP
        include_once( "CELConfig/CELConfig.inc" );
        /*
          Archive   : recuperaDAML.php
          Version      : 1.0
          Comentary: This program lists all the files generated DAML $ _SESSION ['directory']
         */

        function extrair_data($archive_name) {
            list($project, $rest) = split("__", $archive_name);
            list($day, $month, $year, $hour, $minuto, $second, $extension) = split('[_-.]', $rest);

            if (!is_numeric($day) || !is_numeric($month) || !is_numeric($year) || !is_numeric($hour) || !is_numeric($minuto) || !is_numeric($second))
                return "-";

            $month_unabbreviated = "-";
            switch ($month) {
                case 1: $month_unabbreviated = "janeiro";
                    break;
                case 2: $month_unabbreviated = "fevereiro";
                    break;
                case 3: $month_unabbreviated = "mar�o";
                    break;
                case 4: $month_unabbreviated = "abril";
                    break;
                case 5: $month_unabbreviated = "maio";
                    break;
                case 6: $month_unabbreviated = "junho";
                    break;
                case 7: $month_unabbreviated = "julho";
                    break;
                case 8: $month_unabbreviated = "agosto";
                    break;
                case 9: $month_unabbreviated = "setembro";
                    break;
                case 10: $month_unabbreviated = "outubro";
                    break;
                case 11: $month_unabbreviated = "novembro";
                    break;
                case 12: $month_unabbreviated = "dezembro";
                    break;
            }

            return $day . " de " . $month_unabbreviated . " de " . $year . " �s " . $hour . ":" . $minuto . "." . $second . "\n";
        }

        function extrair_projeto($archive_name) {
            list($project) = split("__", $archive_name);
            return $project;
        }

        $directory = $_SESSION['diretorio'];
        $site = $_SESSION['site'];

        if ($directory == "") {
            //    $diretorio = "teste"; 
            $directory = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
        }

        if ($site == "") {
            //    $site = "http://139.82.24.189/cel_vf/aplicacao/teste/";
            $site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
            if ($site == "http:///") {
                print( "Aten��o: O arquivo de configura��o do CELConfig (padr�o: config2.conf) precisa ser configurado corratamente.<BR>\n * N�o foram preenchidas as vari�veis 'HTTPD_ip','CEL_dir_relativo' e 'DAML_dir_relativo_ao_CEL'.<BR>\nPor favor, verifique o arquivo e tente novamente.<BR>\n");
            }
        }

        //Mounts the file table DAML
        print( "<CENTER><TABLE WIDTH=\"80%\">\n");
        print( "<TR>\n\t<Th><STRONG>Projeto</STRONG></Th>\n\t<Th><STRONG>Gerado em</STRONG></Th>\n</TR>\n");
        if ($dir_handle = @opendir($directory)) {
            while (( $archive = readdir($dir_handle) ) !== false) {
                if (is_file($directory . "/" . $archive) && $archive != "." && $archive != "..") {
                    print( "<TR>\n");
                    print( "\t<TD WIDTH=\"25%\" CLASS=\"Estilo\"><B>" . extrair_projeto($archive) . "</B></TD>\n");
                    print( "\t<TD WIDTH=\"55%\" CLASS=\"Estilo\">" . extrair_data($archive) . "</TD>\n");
                    print( "\t<TD WIDTH=\"10%\" >[<A HREF=\"" . $site . $archive . "\">Abrir</A>]</TD>\n");
                    print( "</TR>\n");
                }
            }
            closedir($dir_handle);
        }
        print("</TABLE></CENTER>\n");
        ?> 
    </BODY> 
</HTML> 