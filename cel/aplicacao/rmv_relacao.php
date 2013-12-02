<?php
// Setting - Delete Concept
// Purpose: Allow User to Delete a concept that is active
// Context: User want to delete a concept
// Preconditions: Login, Scenario registered in the system
// Actors: User, System
// Resource: Data informed
// Episodes: The system will provide a screen for the user to justify the need for that
// Exclusion so that the administrator can read and approve or disapprove the same.
// This will also contain a button to confirm the deletion.
// Restrictions: After clicking the button, the system checks whether all fields were filled
// Exceptions: If all fields are empty, returns to the user a message
// Warning that all fields must be filled, and a back button
// To the previous page.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");
include("functionsLexic/inserir_Pedido_Remover_Relacao.php");

$id_relation = 0;
inserirPedidoRemoverRelacao($_SESSION['id_projeto_corrente'], $id_relation, $_SESSION['id_usuario_corrente']);
?>  

<script type="text/javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacao efetuada com sucesso!</h4>

<script type="text/javascript1.3">

    self.close();

</script>
