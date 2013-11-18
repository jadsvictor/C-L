<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("recarrega"))) {
    function recarrega($url) {
        //test if a variable has the correct type
        assert(is_string($url));
        //test if the variable is not null
        assert($url != NULL);
        
        ?>
        <script language="javascript1.3">
            location.replace('<?= $url ?>');
        </script>
       <?php
    }
}

?>
