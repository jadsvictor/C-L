<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("recarrega"))) {
    function recarrega($url) {
        ?>
        <script language="javascript1.3">
            location.replace('<?= $url ?>');
        </script>
       <?php
    }
}

?>
