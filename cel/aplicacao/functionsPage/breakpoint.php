<?php

include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

if (!(function_exists("breakpoint"))) {
    function breakpoint($num) {
        //test if the variable is not null
        assert($num != NULL);
        ?>
        <script language="javascript1.3">
            alert('<?= $num ?>');
        </script>
        <?php
    }
}
?>
