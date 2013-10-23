<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

#
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

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) {

    function tratarPedidoCenario($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        //print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_cenario = $record['id_cenario'];
                $id_projeto = $record['id_projeto'];
                removeCenario($id_projeto, $id_cenario);
                //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
            } else {

                $id_projeto = $record['id_projeto'];
                $titulo = $record['titulo'];
                $objetivo = $record['objetivo'];
                $contexto = $record['contexto'];
                $atores = $record['atores'];
                $recursos = $record['recursos'];
                $excecao = $record['excecao'];
                $episodios = $record['episodios'];
                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_cenario'];
                    removeCenario($id_projeto, $id_cenario);
                    //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
                }
                adicionar_cenario($id_projeto, $titulo, $objetivo, $contexto, $atores, $recursos, $excecao, $episodios);
            }
            //$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
        }
    }

}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o lexico e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoLexico"))) {

    function tratarPedidoLexico($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $selectSin = new QUERY($DB);
        $select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_lexico = $record['id_lexico'];
                $id_projeto = $record['id_projeto'];
                removeLexico($id_projeto, $id_lexico);
            } else {
                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];
                $nocao = $record['nocao'];
                $impacto = $record['impacto'];
                $classificacao = $record['tipo'];

                //sinonimos

                $sinonimos = array();
                $selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $selectSin->gofirst();
                if ($selectSin->getntuples() != 0) {
                    while ($sinonimo != 'LAST_RECORD_REACHED') {
                        $sinonimos[] = $sinonimo["nome"];
                        $sinonimo = $selectSin->gonext();
                    }
                }

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_lexico = $record['id_lexico'];
                    alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao);
                } else if (($idLexicoConflitante = adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)) <= 0) {
                    $idLexicoConflitante = -1 * $idLexicoConflitante;

                    $selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);

                    $row = $selectLexConflitante->gofirst();

                    return $row["nome"];
                }
            }
            return null;
        }
    }

}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoConceito"))) {

    function tratarPedidoConceito($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_conceito = $record['id_conceito'];
                $id_projeto = $record['id_projeto'];
                removeConceito($id_projeto, $id_conceito);
            } else {

                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];
                $descricao = $record['descricao'];
                $namespace = $record['namespace'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_cenario = $record['id_conceito'];
                    removeConceito($id_projeto, $id_conceito);
                }
                adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
            }
        }
    }

}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoRelacao"))) {

    function tratarPedidoRelacao($id_pedido) {
        $DB = new PGDB ();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0) {
            echo "<BR> [ERRO]Pedido invalido.";
        } else {
            $record = $select->gofirst();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido, 'remover')) {
                $id_relacao = $record['id_relacao'];
                $id_projeto = $record['id_projeto'];
                removeRelacao($id_projeto, $id_relacao);
            } else {

                $id_projeto = $record['id_projeto'];
                $nome = $record['nome'];

                if (!strcasecmp($tipoPedido, 'alterar')) {
                    $id_relacao = $record['id_relacao'];
                    removeRelacao($id_projeto, $id_relacao);
                }
                adicionar_relacao($id_projeto, $nome);
            }
        }
    }

}
?>
