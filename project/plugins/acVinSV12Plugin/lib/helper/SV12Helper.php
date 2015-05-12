<?php

function contrat_get_words($contrats) {
    $words = array();
        
    foreach($contrats as $contrat) {
        $words[contrat_get_id($contrat)] = contrat_get_word($contrat);
    }

    return $words;
}

function contrat_get_word($contrat) {
    return array_merge(
        Search::getWords($contrat->produit_libelle),
        Search::getWords($contrat->vendeur_nom),
        Search::getWords($contrat->vendeur_identifiant),
        Search::getWords($contrat->contrat_numero),
        Search::getWords($contrat->contrat_type)
    );
}

function contrat_get_id($contrat) {

    return 'contrat_'.$contrat->contrat_numero;
}