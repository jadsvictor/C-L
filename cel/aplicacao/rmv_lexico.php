<?php
// Scenarios - Delete Lexicon
// Purpose: Allow User to Delete a word from the lexicon that is active
// Context: User want to delete a word from the lexicon
// Precondition: login word lexicon registered in the system
// Actors: User, System
// Resource: Data informed
// Episodes: The system will provide a screen for the user to justify the need
// That exclusion so that the administrator can read and approve or not.
// This will also contain a button to confirm the deletion.
// Restriction: After you clicked the button, the system checks whether all fields were filled
// Exception: If all fields are empty, returns to the user
// A message that all fields must be filled
// And a button to return to the previous page.

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include("functionsLexic/inserir_Pedido_Remover_Lexico.php");
include("functionsBD/check_User_Authentication.php");

checkUserAuthentication("index.php");  

$id_project = 0;
$id_lexicon = 0;
inserirPedidoRemoverLexico($id_project, $id_lexicon, $_SESSION['id_usuario_corrente']);
?>  

<script type="text/javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Operacao efetuada com sucesso!</h4>

<script type="text/javascript1.3">

    self.close();

</script>
