<?php

###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) {

    function removeRelacao($id_projeto, $id_relacao) {
        $DB = new PGDB ();

        $sql6 = new QUERY($DB);

		assert($id_projeto != null);
		assert($id_relacao != null);		
		
        # Remove o conceito escolhido
        $sql6->execute("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
        
        assert($id_relacao = null);
    }

}
?>
