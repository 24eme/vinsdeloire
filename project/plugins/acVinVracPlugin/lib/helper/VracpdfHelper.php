<?php
function getDateValidation($vrac){
    if(!$vrac->exist('valide') || !$vrac->valide->exist('date_saisie') || !$vrac->valide->date_saisie){
        return "Contrat non validé";
    }
    return $vrac->valide->date_saisie;
}

function getPrefixPoint($str,$size){
    if(strlen($str) > $size){
        return substr($str,0, strlen($str)-3).'...';
    }
    $res = '';
    $nb_char = ($str === null)? 0 : strlen($str);
    for ($i = 0; $i < $size - $nb_char; $i++) {
       $res.='.'; 
    }
    $res.=$str; 
    return $res;
}


function vracTypeExplication($vrac){
    switch ($vrac->type_transaction) {
            case VracClient::TYPE_TRANSACTION_VIN_VRAC :
                return "Le prix payé est exprimé en euros par hectolitre";
            case VracClient::TYPE_TRANSACTION_MOUTS :
                return "Le prix payé est exprimé en euros par hectolitre";
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
                return "Le prix payé est exprimé en euros pour une bouteille, un BIB® et sa contenance (comprenant le vin, la mise, les matières sèches ...)";
            case VracClient::TYPE_TRANSACTION_RAISINS :
                return "Le prix payé est exprimé en euros par kilogramme de raisin";
            default:
                return null;
        }    
}

function getMandataireVisa($vrac) {
    if(!$vrac->mandataire_exist){
        return "";
    }
    if(!$vrac->valide->exist('date_signature_courtier')){
        return "";
    }
    if(!$vrac->valide->date_signature_courtier){
        return "";
    }    
    return date("d/m/Y à H\hi", strtotime($vrac->valide->date_signature_courtier));
}

function getDateSignatureVendeur($vrac) {
    if(!$vrac->valide->exist('date_signature_vendeur')){
        return "";
    }
    if(!$vrac->valide->date_signature_vendeur){
        return "";
    }
    return date("d/m/Y à H\hi", strtotime($vrac->valide->date_signature_vendeur));
}

function getDateSignatureAcheteur($vrac) {
     if(!$vrac->valide->exist('date_signature_acheteur')){
        return "";
    }
    if(!$vrac->valide->date_signature_acheteur){
        return "";
    }
     return date("d/m/Y à H\hi", strtotime($vrac->valide->date_signature_acheteur));
}

function getPrixTouteLettre($vrac){
    $str_price = "".$vrac->prix_initial_total;
    if(preg_match('/^([0-9]*)(\.?[0-9]*)$/', $str_price, $matches)){
        $price = '~';
        if(!is_null($price_rounded = $matches[1])){
            if(intval($price_rounded) > 999999){
                $price_rounded_supmillion = substr($price_rounded,0, -6);
                $price.="\\numberstringnum{".$price_rounded_supmillion."}~million";
                if($price_rounded_supmillion > 1){
                   $price.="s"; 
                }
                $price_rounded_supmill = substr($price_rounded,0, -3);
                $price_rounded_infmill = substr($price_rounded, -3);
                $price.="\\numberstringnum{".$price_rounded_supmill."}~mille~\\numberstringnum{".$price_rounded_infmill."}~euro";
            }elseif(intval($price_rounded) > 9999){
                $price_rounded_supmill = substr($price_rounded,0, -3);
                $price_rounded_infmill = substr($price_rounded, -3);
                $price.="\\numberstringnum{".$price_rounded_supmill."}~mille~\\numberstringnum{".$price_rounded_infmill."}~euro";
            }else{
                $price.="\\numberstringnum{".$price_rounded."}~euro";
            }
            if($price_rounded > 0){
                $price.="s";
            }
        }
        if(!is_null($price_rounded_cents = $matches[2]) && $price_rounded_cents!="" && $price_rounded_cents > 0){
            $price.="~et~\\numberstringnum{".substr($price_rounded_cents,1)."}~ centimes";
        }
        return $price."~";
    }
    return "~0 euro~";
}

function getCheckBoxe($b) {
    return ($b)? '\squareChecked' : '$\square$';
}

function getContenancePdf($vrac){
      if($vrac->type_transaction == VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE) {
          if(preg_match('/cl/', $vrac->bouteilles_contenance_libelle)){
            if(preg_match('/Bouteille/', $vrac->bouteilles_contenance_libelle)){
              return '('.$vrac->bouteilles_contenance_libelle.')';
            }else
              return '(Bouteille '.$vrac->bouteilles_contenance_libelle.')';  
          }
          else
          {
              return '('.str_replace('BIB', 'BIB®', $vrac->bouteilles_contenance_libelle).')';  
          }
        }    
        return "~";
}