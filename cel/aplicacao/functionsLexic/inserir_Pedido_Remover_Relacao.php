<?php

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao"))) {

    function inserirPedidoRemoverRelacao($id_projeto, $id_relacao, $id_usuario) {
        $DB = new PGDB ();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao");
        $relacao = $select->gofirst();
        $nome = $relacao['nome'];

        $insere->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)");
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
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!", "From: $nome\r\n" . "Reply-To: $email\r\n");
                $record2 = $select2->gonext();
            }
        }
    }

}
?>
