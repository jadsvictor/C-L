<?php
include("bd.inc");
include_once("CELConfig/CELConfig.inc");

session_start();


// Scenario - Perform logout
// Purpose: Allow the user to perform the logout, maintaining the integrity of what was
// Done, and returns to the login screen
// Context: Open System. User has accessed the system.
// User wishes to exit the application and maintain the integrity of which was
// Done
// Precondition: User has accessed the system
// Actors: User, System.
// Features: Interface
// Episodes: The system closes the user session, maintaining the integrity of what was done
// The system returns the login interface, allowing the user to login
// Again	

session_destroy();
session_unset();
$ip_value = CELConfig_ReadVar("HTTPD_ip");
?>

<html>
    <script language="javascript1.3">


        document.writeln('<p style="color: blue; font-weight: bold; text-align: center">A aplica��o teminou escolha uma das op��es abaixo:</p>');
        document.writeln('<p align="center"><a href="javascript:logoff();">Entrar novamente</a></p>');
        document.writeln('<p align="center"><a href="http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . "../"); ?>">P�gina inicial</a></p>');
        document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        function logoff()
        {
            location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
        }


    //window.close();
    //location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
    </script>
</html>

