<?php

###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico"))) {

    function removeLexico($id_projeto, $id_lexico) {
        $DB = new PGDB ();
        $delete = new QUERY($DB);

		assert($id_projeto != null);
		assert($id_lexico != null);		
		
        # Remove o relacionamento entre o lexico a ser removido
        # e outros lexicos que o referenciam
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico");
        $delete->execute("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico");
        $delete->execute("DELETE FROM centolex WHERE id_lexico = $id_lexico");

        # Remove o lexico escolhido
        $delete->execute("DELETE FROM sinonimo WHERE id_lexico = $id_lexico");
        $delete->execute("DELETE FROM lexico WHERE id_lexico = $id_lexico");
        
        assert($id_lexico = null);
    }

}
?>
