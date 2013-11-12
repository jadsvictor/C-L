<?php
###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################

include("functionsLexic/remove_Lexico.php");

if (!(function_exists("inserirPedidoRemoverLexico"))) {

    function inserirPedidoRemoverLexico($id_projeto, $id_lexico, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        
        assert($DB!=null);
		assert($insere!=null);
		assert($select!=null);
		assert($select2!=null);
		assert($id_projeto!=null);
		assert($id_lexico!=null);
		assert($id_usuario!=null);

        $qr = mysql_query("SELECT * FROM participa WHERE gerente = 1 
            AND id_usuario =" . _GET('$id_usuario') . " 
            AND id_projeto =" . _GET('$id_projeto')) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
       
        $resultArray = mysql_fetch_array($qr);

        if ($resultArray == false) { //nao e gerente

            $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico");
            $lexico = $select->gofirst();
            $nome = $lexico['nome'];

            $insere->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {
                $record = $select->gofirst();
                $nome = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { // e gerente
            removeLexico($id_projeto, $id_lexico);
        }
    }

}
?>
