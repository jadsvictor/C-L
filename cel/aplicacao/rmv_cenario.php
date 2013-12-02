<?php
// Setting - Delete Scenario
// Purpose: Allow User to Delete a scenario that is active
// Context: User want to delete a Scenario
// Preconditions: Login, Scenario registered in the system
// Actors: User, System
// Resource: Data informed
// Episodes: The system will provide a screen for the user to justify the need for that
// Exclusion so that the administrator can read and approve or disapprove the same.
// This will also contain a button to confirm Exclusion.
// Restrictions: After clicking the button, the system checks whether all fields were filled
// Exception: If all the pounds in the Fields were filled, returns to the User a message
// Warning that all fields must be completed and a button to return the
// To the previous page.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");

$id_scene = 0;
inserirPedidoRemoverCenario($_SESSION['id_projeto_corrente'], $id_scene, $_SESSION['id_usuario_corrente']);
?>  

<script type="text/javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacaoo efetuada com sucesso!</h4>

<script type="text/javascript1.3">

    self.close();

</script>
