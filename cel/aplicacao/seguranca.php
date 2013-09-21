<?php

function escapes_metacharacters($string) {
    $string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
    return $string;
}

function prepares_data($string) {
    //Remove espacos em branco do inicio e do fim da string
    //$string = trim( $string );
    // Substitui o & por amp; (para que nao de problemas ao gerar o XML)

    $string = ereg_replace("&", "&amp;", $string);

    // Retira tags html e php da string

    $string = strip_tags($string);

    // Verifica se a diretiva get_magic_quotes_gpc() esta ativada, se estiver a funcao stripslashes e utilizada na string

    $string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
    $string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
    return $string;
}

?>
