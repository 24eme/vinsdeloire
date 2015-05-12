<?php

function produit_get_words($produits) {
    $words = array();
        
    foreach($produits as $produit) {
        $words[produit_get_id($produit)] = produit_get_word($produit);
    }

    return $words;
}

function produit_get_word($produit) {
    return array_merge(
        Search::getWords($produit->mois),
        Search::getWords($produit->produit_libelle),
	array(str_replace(' ', '_', $produit->produit_libelle))
    );
}

function produit_get_id($produit) {

    return 'produit_'.$produit->periode.str_replace('/', '-', $produit->produit_hash);
}