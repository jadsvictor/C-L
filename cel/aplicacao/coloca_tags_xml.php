<?php

  // Modulo que poe as tags dos links no arquivo XML

include ("coloca_links.php");

function poe_tag_xml($str) {

    $r = "<link ref=\"$str\">$str </link>";
    return $r;
}

function pega_id_xml($str) {

    $j = 0;
    $i = 0;
    while ($str[$i] != '*') {
        $buffer[$j] = $str[$i];
        $i++;
        $j++;
    }

    return implode('', $buffer);
}

function troca_chaves_xml($str) {
    $conta_abertos = 0;
    $conta_fehados = 0;
    $comeco = 0;
    $fim = 0;
    $x = 0;
    $y = 0;
    $vet_id = 0;
    $link_original = 0;
    $link_novo = 0;
    $buffer3 = '';
    $buffer = 0;
    $i = 0;
    $tam_str = strlen($str);

    while ($i <= $tam_str) {
        if ($str[$i] == '}') {
            $conta_abertos = $conta_abertos + 1;
        }
        $i++;
    }
    while ($i <= $tam_str) {
        if ($str[$i] == '}') {
            $conta_fechados = $conta_fechados + 1;
        }
        $i++;
    } 
    
    if ($conta_abertos == 0) {
        return $str;
    }
    
    while ($i <= $tam_str) {
        if ($str[$i] == '{') {
            $buffer = $buffer + 1;
            if ($buffer == 1) {
                $comeco[$x] = $i;
                $x++;
            }
        }
        if ($str[$i] == '}') {
            $buffer = $buffer - 1;
            if ($buffer == 0) {
                $fim[$y] = $i + 1;
                $y++;
            }
        }
        $i++;
    };
    
    while ($i < $x) { //x = numero de links reais - 1    
        $link = substr($str, $comeco[$i], $fim[$i] - $comeco[$i]);
        $link_original[$i] = $link;
        $link = str_replace('{', '', $link);
        $link = str_replace('}', '', $link);
        $buffer2 = 0;
        $conta = 0;
        $n = 0;
        $vet_id[$i] = pega_id_xml($link);
        $link = '**' . $link;
        $marcador = 0;

        while ($n < $fim[$i] - $comeco[$i]) {
            if ($link[$n] == '*' && $link[$n + 1] == '*' && $marcador == 1) {
                $marcador = 0;
                $link[$n] = '{';
                $link[$n + 1] = '{';
                $n++;
                $n++;
                continue;
            }

            if ($link[$n] == '*' && $link[$n + 1] == '*') {
                $marcador = 1;
                $link[$n] = '{';
                $n++;
                continue;
            }

            if ($marcador == 1) {
                $link[$n] = '{';
            }
            $n++;
        }
        $link = str_replace('{', '', $link);
        $link = poe_tag_xml($link, $vet_id[$i]);
        $link_novo[$i] = $link;
        $i++;
    }
    
    while ($i < $x) {
        $str = str_replace($link_original[$i], $link_novo[$i], $str);
        $i++;
    }
    
    return $str;
}

function faz_links_XML($texto, $vetor_lex, $vetor_cen) {

    marca_texto($texto, $vetor_cen, "cenario");
    marca_texto_cenario($texto, $vetor_lex, $vetor_cen);

    $str = troca_chaves_xml($texto);
    return $str;
}
?> 
