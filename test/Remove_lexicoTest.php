<?php

//Classe responsável por realizar o teste da classe inclui_lexico.

include("functionsLexic/remove_Lexico.php");
class Remove_lexicoTest extends PHPUnit_Framework_TestCase {

    protected $idProjeto;
    protected $nome;
    protected $nocao;
    protected $impacto;
    protected $sinonimos;
    protected $classificacao;

    public function setUp() {
        $this->idProjeto= 1;
        $this->nome = "Teste_Nome";
        $this->nocao = "Teste_Nocao";
        $this->impacto = "Teste_Impacto";
        $this->sinonimos = "Teste_Sinonimo";
        $this->classificados = "Teste_Classificado";
    }

    /**
     * @test
     *
     */
    public function teste_remove_lexico() {
        $this->assertEquals(true, removeLexico($this->idProjeto, null, $this->nome));
        
    }
}

?>
