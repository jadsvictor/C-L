<?php

$_SESSION["estruturas"] = 1;

class conceito {

    var $nome = 0;
    var $descricao = 0;
    var $relacoes = 0;
    var $subconceitos = 0;
    var $namespace = 0;

    function conceito($n, $d) {
        $this->nome = $n;
        $this->descricao = $d;
        $this->relacoes = array();
        $this->subconceitos = array(); //not initialized
        $this->namespace = "";
    }

}

class relacao_entre_conceitos {

    var $predicados = 0;
    var $verbo = 0;

    function relacao_entre_conceitos($p, $v) {
        $this->predicados[] = $p;
        $this->verbo = $v;
    }

}

class termo_do_lexico {

    var $nome = 0;
    var $nocao = 0;
    var $impacto = 0;

    function termo_do_lexico($name, $notion, $i) {
        $this->nome = $name;
        $this->nocao = $notion;
        $this->impacto = $i;
    }

}

?>