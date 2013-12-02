<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

#############################################
# Formats the date
# Receives YYY-DD-MM
# Returns DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) {

    function formataData($date) {

        //test if the variable is not null
        assert($date != null);

        $newDate = substr($date, 8, 9) .
                substr($date, 4, 4) .
                substr($date, 0, 4);
        return $newDate;
    }

} else {
    //nothing to do
}
?>
