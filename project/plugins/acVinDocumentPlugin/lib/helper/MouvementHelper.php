<?php

function mouvement_get_words($mouvements) {
    $words = array();
        
    foreach($mouvements as $mouvement) {
        $words[mouvement_get_id($mouvement)] = mouvement_get_word($mouvement);
    }

    return $words;
}

function mouvement_get_word($mouvement) {
    return array_merge(
        Search::getWords($mouvement->produit_libelle),
        Search::getWords($mouvement->type_libelle),
        Search::getWords($mouvement->detail_libelle),
        Search::getWords($mouvement->vrac_numero),
        Search::getWords($mouvement->vrac_destinataire),
	array(str_replace(' ', '_', $mouvement->produit_libelle))
    );
}

function mouvement_get_id($mouvement) {

    return str_replace("/", '-', $mouvement->id);
}