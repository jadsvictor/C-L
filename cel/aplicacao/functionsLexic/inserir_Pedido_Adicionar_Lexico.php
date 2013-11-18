<?php

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################

include("functionsLexic/adicionar_lexico.php");

if (!(function_exists("inserirPedidoAdicionarLexico"))) {

    function inserirPedidoAdicionarLexico($id_projeto, $nome, $nocao, $impacto, $id_usuario, $sinonimos, $classificacao) {

        $DB = new PGDB();
        $insere = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
       
      	assert($insere!=null); 
        assert($DB!=null);
		assert($select!=null);
		assert($select2!=null);
		assert($id_projeto!=null);
		assert($nome!=null);
		assert($nocao!=null);
		assert($impacto!=null);
		assert($id_usuario!=null);
		assert($sinonimos!=null);
		assert($classificacao!=null);

        $q = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $qr = mysql_query($q) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($qr);


        if ($resultArray == false) { //nao e gerente

            $insere->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,'$nome','$nocao','$impacto','$classificacao',$id_usuario,'inserir',0)");

            $newId = $insere->getLastId();

            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");

            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");


            //insere sinonimos

            foreach ($sinonimos as $sin) {
                $insere->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
				VALUES ($newId, '" . prepares_data(strtolower($sin)) . "', $id_projeto)");
            }
            //fim da insercao dos sinonimos

            if ($select->getntuples() == 0 && $select2->getntuples() == 0) {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail.";
            } else {

                $record = $select->gofirst();
                $nome2 = $record['nome'];
                $email = $record['email'];
                $record2 = $select2->gofirst();
                while ($record2 != 'LAST_RECORD_REACHED') {
                    $id = $record2['id_usuario'];
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst();
                    $mailGerente = $record['email'];
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico $nome \nObrigado!", "From: $nome2\r\n" . "Reply-To: $email\r\n");
                    $record2 = $select2->gonext();
                }
            }
        } else { //Eh gerente
            adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }

}
?>
