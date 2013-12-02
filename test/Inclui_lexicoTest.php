<?php

//Classe responsável por realizar o teste da classe inclui_lexico.

include("functionsLexic/inclui_lexico.php");
class Inclui_lexicoTest extends PHPUnit_Framework_TestCase {

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
    public function teste_inclui_lexico() {
        $retorno = inclui_lexico($this->idProjeto,$this->nome,$this->nocao,$this->impacto,$this->sinonimos,$this->classificados);
        $this->assertNotNull($retorno);
        
    }
}

?>
