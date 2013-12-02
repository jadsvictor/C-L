<?php

//This module has functions to save a file in a DAML Ontology

include 'estruturas.php';
session_start();

// Standard for date
$dia = date("Y-m-d");
$hora = date("H:i:s");

// Objective: Save the ontology in DAML

function salva_daml($url_ontologia, $diretorio, $arquivo, $array_info, $lista_de_conceitos, $lista_de_relacoes, $lista_de_axiomas) {
    // Records the URL of Ontology
    $url = $url_ontologia . $arquivo;

    // Registers the path to the DAML
    $caminho = $diretorio . $arquivo;

    // Creates a new file DAML
    if (!$fp = fopen($caminho, "w"))
        return FALSE;

    // Writes standard header file DAML 
    $cabecalho = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    $cabecalho = $cabecalho . '<rdf:RDF xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:xsd="http://www.w3.org/2000/10/XMLSchema#" xmlns:';
    $cabecalho = $cabecalho . $array_info['title'] . '="' . $url . '#">';
    if (!fwrite($fp, $cabecalho))
        return FALSE;


    // enter information ontology 
    $info = '<daml:Ontology rdf:about="">';
    if ($array_info ["title"] == "")
        $info = $info . '<dc:title />';
    else
        $info = $info . '<dc:title>' . $array_info ["title"] . '</dc:title>';
    $info = $info . '<dc:date>' . date("j-m-Y  H:i:s") . '</dc:date>';
    if ($array_info ["creator"] == "")
        $info = $info . '<dc:creator />';
    else
        $info = $info . '<dc:creator>' . $array_info ["creator"] . '</dc:creator>';
    if ($array_info ["description"] == "")
        $info = $info . '<dc:description />';
    else
        $info = $info . '<dc:description>' . $array_info ["description"] . '</dc:description>';
    if ($array_info ["subject"] == "")
        $info = $info . '<dc:subject />';
    else
        $info = $info . '<dc:subject>' . $array_info ["subject"] . '</dc:subject>';
    if ($array_info ["versionInfo"] == "")
        $info = $info . '<daml:versionInfo />';
    else
        $info = $info . '<daml:versionInfo>' . $array_info ["versionInfo"] . '</daml:versionInfo>';
    $info = $info . '</daml:Ontology>';
    if (!fwrite($fp, $info))
        return FALSE;


    //  Inserts the concepts, relations and axioms
    if (!grava_conceitos($fp, $url, $lista_de_conceitos, $array_info ["creator"]))
        return FALSE;
    if (!grava_relacoes($fp, $url, $lista_de_relacoes, $array_info ["creator"]))
        return FALSE;
    if (!grava_axiomas($fp, $url, $lista_de_axiomas, $array_info ["creator"]))
        return FALSE;


    // Inserts the closing tag header
    if (!fwrite($fp, '</rdf:RDF>'))
        return FALSE;

    fclose($fp);

    return $arquivo;
}

//Purpose: Write the concepts on file DAML

function grava_conceitos($fp, $url, $lista_de_conceitos, $criador) {

    foreach ($lista_de_conceitos as $oConceito) {

        if ($oConceito->namespace == "proprio") {
            $namespace = "";
        } else {
            $namespace = $oConceito->namespace;
        }
        $s_conc = '<daml:Class rdf:about="' . $namespace . '#' . $oConceito->nome . '">';
        $s_conc = $s_conc . '<rdfs:label>' . strip_tags($oConceito->nome) . '</rdfs:label>';
        $s_conc = $s_conc . '<rdfs:comment><![CDATA[' . strip_tags($oConceito->descricao) . ']]> ' . '</rdfs:comment>';
        $s_conc = $s_conc . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_conc = $s_conc . '<creator><![CDATA[' . $criador . ']]> ' . '</creator>';
        if (!fwrite($fp, $s_conc))
            return FALSE;


        // Search by concept-father (SubConceptOf) 
        $lista_subconceitos = $oConceito->subconceitos;
        foreach ($lista_subconceitos as $subconceito) {
            $s_subconc = '<rdfs:subClassOf>';
            $s_subconc = $s_subconc . '<daml:Class rdf:about="' . $url . '#' . strip_tags($subconceito) . '" />';
            $s_subconc = $s_subconc . '</rdfs:subClassOf>';
            if (!fwrite($fp, $s_subconc))
                return FALSE;
        }

        // List the relationships between concepts 
        $lista_relacoes = $oConceito->relacoes;
        foreach ($lista_relacoes as $relacao) {
            $s_relac = '<rdfs:subClassOf>';
            $s_relac = $s_relac . '<daml:Restriction>';
            $lista_predicados = $relacao->predicados;
            foreach ($lista_predicados as $predicado) {
                $s_relac = $s_relac . '<daml:onProperty rdf:resource="' . '#' . strip_tags($relacao->verbo) . '" />';
                $s_relac = $s_relac . '<daml:hasClass>';
                $s_relac = $s_relac . '<daml:Class rdf:about="' . '#' . strip_tags($predicado) . '" />';
                $s_relac = $s_relac . '</daml:hasClass>';
            }
            $s_relac = $s_relac . '</daml:Restriction>';
            $s_relac = $s_relac . '</rdfs:subClassOf>';
            if (!fwrite($fp, $s_relac))
                return FALSE;
        }

        $s_conc = '</daml:Class>';
        if (!fwrite($fp, $s_conc))
            return FALSE;
    }
    return TRUE;
}

// Purpose:        Save the file in relations DAML

function grava_relacoes($PonteiroArquivoDAML, $lista_de_relacoes, $criador) {
    foreach ($lista_de_relacoes as $relacao) {
        $s_rel = '<daml:ObjectProperty rdf:about="' . "#" . strip_tags($relacao) . '">';
        $s_rel = $s_rel . '<rdfs:label>' . $relacao . '</rdfs:label>';
        $s_rel = $s_rel . '<creationDate><![CDATA[' . $GLOBALS["data"] . ']]> ' . '</creationDate>';
        $s_rel = $s_rel . '<creator><![CDATA[' . $criador . ']]> ' . '</creator>';
        $s_rel = $s_rel . '</daml:ObjectProperty>';
        if (!fwrite($PonteiroArquivoDAML, $s_rel))
            return FALSE;
    }
    return TRUE;
}

// Purpose: Save the file DAML axioms

function grava_axiomas($PonteiroArquivoDAML, $url, $lista_de_axiomas) {
    foreach ($lista_de_axiomas as $axioma) {

        $axi = explode(" disjoint ", $axioma);
        $s_axi = '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[0]) . '">';
        $s_axi = $s_axi . '<daml:disjointWith>';
        $s_axi = $s_axi . '<daml:Class rdf:about="' . $url . '#' . strip_tags($axi[1]) . '" />';
        $s_axi = $s_axi . '</daml:disjointWith>';
        $s_axi = $s_axi . '</daml:Class>';
        if (!fwrite($PonteiroArquivoDAML, $s_axi))
            return FALSE;
    }

    return TRUE;
}

?>