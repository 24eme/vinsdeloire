<?php

function display_teledeclaration_soussigne_NomCvi($object){
    if(!$object){
        echo "";
        return;
    }
    $result = $object->nom;
    if($object->cvi){
        $result.= '&nbsp('.$object->cvi.')';
    }
    echo $result;
}

function getCvoLabels($label) {
    $cvo_nature = array(VracClient::CVO_NATURE_MARCHE_DEFINITIF => 'Marché définitif',
        VracClient::CVO_NATURE_COMPENSATION => 'Compensation',
        VracClient::CVO_NATURE_NON_FINANCIERE => 'Non financière',
        VracClient::CVO_NATURE_VINAIGRERIE => 'Vinaigrerie');
    return $cvo_nature[$label];
}

function dateCampagneViticolePresent() {
    $date = date('mY');
    $mois = substr($date, 0, 2);
    $annee = substr($date, 2, 6);
    $campagne = ($mois < 8) ? ($annee - 1) . '/' . $annee : $annee . '/' . ($annee + 1);
    return $campagne;
}

function dateCampagneViticole($date) {
    $date_exploded = explode("/", $date);
    $mois = $date_exploded[1];
    $annee = $date_exploded[2];
    $campagne = ($mois < 8) ? ($annee - 1) . '/' . $annee : $annee . '/' . ($annee + 1);
    return $campagne;
}

function isARechercheParam($actifs, $label) {
    return in_array($label, $actifs);
}

function statusColor($status) {

    if (is_null($status))
        return '';

    switch ($status) {
        case VracClient::STATUS_CONTRAT_ANNULE:
            return 'statut_annule';
        case VracClient::STATUS_CONTRAT_SOLDE:
            return 'statut_solde';
        case VracClient::STATUS_CONTRAT_NONSOLDE:
            return 'statut_non-solde';
        default :
            return '';
    }
}

function showRecapPrixUnitaire($vrac) {
    $unite = showPrixUnitaireUnite($vrac);

    if ($vrac->hasPrixVariable() && !$vrac->hasPrixDefinitif()) {
        return sprintf("%s (Prix non définitif)", showRecapPrixUnitaireByUniteAndPrix($unite, $vrac->prix_unitaire, $vrac->getPrixUnitaireHlOuInitial()));
    } elseif ($vrac->hasPrixVariable() && $vrac->hasPrixDefinitif()) {
        return sprintf("%s (Prix initial : %s)", showRecapPrixUnitaireByUniteAndPrix($unite, $vrac->prix_unitaire, $vrac->getPrixUnitaireHlOuInitial()), showRecapPrixUnitaireByUniteAndPrix($unite, $vrac->prix_initial_unitaire, $vrac->prix_initial_unitaire_hl));
    }
    return showRecapPrixUnitaireByUniteAndPrix($unite, $vrac->prix_unitaire, $vrac->getPrixUnitaireHlOuInitial());
}

function showRecapPrixUnitaireByUniteAndPrix($unite, $prix_unitaire, $prix_unitaire_hl) {
    if ($unite == '€/hl') {
        return sprintf('%s €/hl', echoF($prix_unitaire));
    }

    return sprintf('%s %s, soit %s €/hl', echoF($prix_unitaire), $unite, echoF($prix_unitaire_hl));
}

function showPrixUnitaireUnite($vrac) {
    switch ($vrac->type_transaction) {
        case VracClient::TYPE_TRANSACTION_RAISINS:
            return '€/kg';
            break;
        case VracClient::TYPE_TRANSACTION_MOUTS:
            return '€/hl';
            break;
        case VracClient::TYPE_TRANSACTION_VIN_VRAC:
            return '€/hl';
            break;
        case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE:
            return '€/btle';
            break;
    }
}

function showRecapPrixTotal($vrac) {
    if ($vrac->hasPrixVariable() && !$vrac->hasPrixDefinitif()) {

        return sprintf('%s € (Prix non définitif)', echoF($vrac->getPrixTotalOuInitial()));
    }

    if ($vrac->hasPrixVariable() && $vrac->hasPrixDefinitif()) {

        return sprintf('%s € (Prix initial : %s €)', echoF($vrac->getPrixTotal()), echoF($vrac->prix_initial_total));
    }

    return sprintf('%s €', echoF($vrac->getPrixTotalOuInitial()));
}

function showType($vrac) {
    if ($type = $vrac->type_transaction) {
        return showTypeFromLabel($type, '', $vrac);
    }
    return '';
}

function showTypeFromLabel($type, $prefix = 'Type de transaction : ', $vrac = null) {
    switch ($type) {
        case VracClient::TYPE_TRANSACTION_VIN_VRAC: return $prefix . 'Vin en vrac';
        case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE: {
                $bibOrBouteille = ($vrac) ? bouteilleUnitTerm($vrac) : '';
                return $prefix . 'Conditionné ' . $bibOrBouteille;
            }
        case VracClient::TYPE_TRANSACTION_RAISINS: return $prefix . 'Raisins';
        case VracClient::TYPE_TRANSACTION_MOUTS: return $prefix . 'Moûts';
        default: return $type;
    }
}

function showRecapVolumePropose($vrac) {
    if ($type = $vrac->type_transaction) {
        switch ($type) {
            case VracClient::TYPE_TRANSACTION_RAISINS:
                return echoF($vrac->raisin_quantite) . ' kg (raisins), soit ' . echoF($vrac->volume_propose) . ' hl';
            case VracClient::TYPE_TRANSACTION_MOUTS: return echoF($vrac->volume_propose) . ' hl (moûts)';
            case VracClient::TYPE_TRANSACTION_VIN_VRAC: return echoF($vrac->volume_propose) . ' hl (vrac)';
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE:
                return echoF($vrac->bouteilles_quantite) .
                        ' bouteilles (' . $vrac->bouteilles_contenance_libelle . '), soit ' . echoF($vrac->volume_propose) . ' hl';
        }
    }
    return '';
}

function showUnite($vrac) {
    if ($type = $vrac->type_transaction) {
        switch ($type) {
            case VracClient::TYPE_TRANSACTION_RAISINS: return 'kg';
            case VracClient::TYPE_TRANSACTION_MOUTS: return 'hl';
            case VracClient::TYPE_TRANSACTION_VIN_VRAC: return 'hl';
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE: {
                    return bouteilleUnitTerm($vrac, true);
                }
        }
    }
    return '';
}

function typeProduit($type) {
    switch ($type) {
        case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
            return 'Btl';
        case VracClient::TYPE_TRANSACTION_VIN_VRAC :
            return 'V';
        case VracClient::TYPE_TRANSACTION_MOUTS :
            return 'M';
        case VracClient::TYPE_TRANSACTION_RAISINS :
            return 'R';
    }
    return '';
}

function echoF($f) {
    return sprintf("%01.02f", round($f, 2));
}

function vrac_get_words($vracs) {
    $words = array();

    foreach ($vracs as $vrac) {
        $words[vrac_get_id($vrac)] = vrac_get_word($vrac);
    }

    return $words;
}

function vrac_get_word($vrac) {
    
    $num_archive = ($vrac->value[VracClient::VRAC_VIEW_NUMARCHIVE])? $vrac->value[VracClient::VRAC_VIEW_NUMARCHIVE] : '';
    $libelle_produit = ($vrac->value[VracClient::VRAC_VIEW_PRODUIT_ID])? ConfigurationClient::getCurrent()->get($vrac->value[VracClient::VRAC_VIEW_PRODUIT_ID])->getLibelleFormat() : "";
    return array_merge(
            Search::getWords($num_archive),
            Search::getWords($vrac->value[VracClient::VRAC_VIEW_VENDEUR_NOM]),
            Search::getWords($vrac->value[VracClient::VRAC_VIEW_ACHETEUR_NOM]),
            Search::getWords($vrac->value[VracClient::VRAC_VIEW_MANDATAIRE_NOM]),
            Search::getWords($vrac->value[VracClient::VRAC_VIEW_MANDATAIRE_NOM]),
            Search::getWords($libelle_produit)
    );
}

function vrac_get_id($vrac) {

    return 'vrac_' . $vrac->value[VracClient::VRAC_VIEW_NUMCONTRAT];
}

function formatQuantite($vrac, $dec_point = ".", $thousands_sep = "") {
    $quantite = $vrac->getQuantite();
    $decimals = 0;
    $nb_decimals = strlen(preg_replace("/^[0-9]+\.?/", "", $quantite));
    if ($nb_decimals > 0) {
        $decimals = 2;
    }
    switch ($vrac->type_transaction) {
        case VracClient::TYPE_TRANSACTION_RAISINS:

            return number_format($quantite, $decimals, $dec_point, $thousands_sep);
        case VracClient::TYPE_TRANSACTION_MOUTS:

            return number_format($quantite, 2, $dec_point, $thousands_sep);
        case VracClient::TYPE_TRANSACTION_VIN_VRAC:

            return number_format($quantite, 2, $dec_point, $thousands_sep);
        case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE:

            return number_format($quantite, $decimals, $dec_point, $thousands_sep);
    }
}

function formatPrix($prix, $dec_point = ".", $thousands_sep = "") {
    $decimals = 2;

    $nb_decimals = strlen(preg_replace("/^[0-9]+\.?/", "", $prix));
    if ($nb_decimals > 2) {
        $decimals = $nb_decimals;
    }

    return number_format($prix, $decimals, $dec_point, $thousands_sep);
}

function formatPrixFr($prix) {

    return formatPrix($prix, ",", " ");
}

function formatQuantiteFr($vrac) {

    return formatQuantite($vrac, ",", " ");
}

function bouteilleUnitTerm($vrac, $abbr = false) {
    if ($vrac->type_transaction != VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE) {
        return '';
    }
    if (preg_match('/(Bouteille|cl)/', $vrac->bouteilles_contenance_libelle)) {
        return ($abbr) ? 'btles' : 'bouteilles';
    }
    return 'BIB®';
}

function echoPictoSignature($societe, $contrat, $type, $hide = false) {
    if(!$societe || $hide) return '';
    
    if (!$contrat->isTeledeclare()) {
        return;
    }
    $fctName = 'isSigne' . $type;
    $isSigne = $contrat->$fctName();
    if (($societe->type_societe == SocieteClient::SUB_TYPE_VITICULTEUR && $type == 'Vendeur' && $isSigne) || ($societe->type_societe == SocieteClient::SUB_TYPE_NEGOCIANT && $type == 'Acheteur' && $isSigne) || ($societe->type_societe == SocieteClient::SUB_TYPE_COURTIER && $type == 'Courtier' && $isSigne)) {
        echo 'contrat_signe_moi ';
    } else {
        echo ($isSigne) ? 'contrat_signe_soussigne ' : 'contrat_attente ';
    }
}

function getClassStatutPicto($vrac, $isTeledeclarationMode = false) {

    if (!$isTeledeclarationMode && ($vrac->valide->statut == VracClient::STATUS_CONTRAT_NONSOLDE)) {
        return 'statut_non-solde';
    } elseif ($vrac->valide->statut == VracClient::STATUS_CONTRAT_ANNULE) {
        return 'statut_annule';
    }
    elseif ($isTeledeclarationMode && ($vrac->valide->statut == VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE)) {
        return 'statut_non-solde';
    }
     elseif ($vrac->valide->statut == VracClient::STATUS_CONTRAT_BROUILLON) {
        return 'statut_brouillon';
    }
    return 'statut_solde';
    
}

function echoClassLignesVisu(&$cpt){
        echo ($cpt % 2) ? 'ligne_form ' : 'ligne_form ligne_form_alt ';
        $cpt++;
}


