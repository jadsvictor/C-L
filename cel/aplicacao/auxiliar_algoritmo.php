<?php

function existe_relacao($rel, $list) {
    
    //tests if the variable is not null
    assert($rel != NULL);
    assert($list != NULL);
    
    //tests if the variable has the correct type
    assert(is_string($rel));
    assert(is_string($list));
    
    foreach ($list as $key => $relacao) {
        if (@$relacao->verbo == $rel) {
            return $key;
        }
        
        else{
           	//nothing to do
       }
    }
    return -1;
}

function existe_conceito($conc, $list) {
    
        //tests if the variable is not null
        assert($conc != NULL);
        assert($list != NULL);
    
        //tests if the variable has the correct type
        assert(is_string($conc));
        assert(is_string($list));
    
    foreach ($list as $key => $conc1) {
        if ($conc1->nome == $conc) {
            return $key;
        }
        
        else{
           	//nothing to do
        }
    }
    return -1;
}

?>