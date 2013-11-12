<?php

 if (!(function_exists("checkUserAuthentication"))) {

    function checkUserAuthentication($url) {

        if(!(isset($_SESSION["id_usuario_corrente"])))  {
           
            ?>
            <script language="javascript1.3">
                
                open('login.php?url=<?= $url ?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
  
            </script>

            <?php
            exit();
        }
        
        else {
        	
        	//nothing to do        	
        }
    } 
    
 }
 
 else {
        	
        //nothing to do        	
}

?>
