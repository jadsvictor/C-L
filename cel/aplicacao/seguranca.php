<?php

function escapes_metacharacters($string) {
    $string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
    return $string;
}

function prepares_data($string) {

    // Remove blank spaces from the beginning and end of string
    ����// $ string = trim ($ string);
    ����// Replaces by & amp;
    (so not a problem generating the XML)
    $string = ereg_replace("&", "&amp;", $string);

    // Remove html tags and php string
    $string = strip_tags($string);

    // Check if the policy get_magic_quotes_gpc () is enabled, the stripslashes function and if used in string
    $string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
    $string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
    return $string;
}

?>
