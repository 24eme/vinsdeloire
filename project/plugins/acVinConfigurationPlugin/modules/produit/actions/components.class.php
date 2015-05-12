<?php

class produitComponents extends sfComponents {

    public function executeItem() {
        $droit_cvo = $this->produit->getCepage()->getDroits('INTERPRO-inter-loire')->get(ConfigurationDroits::DROIT_CVO);
        
        try {
            $this->cvo = $this->produit->getCepage()->getDroits('INTERPRO-inter-loire')->get(ConfigurationDroits::DROIT_CVO)->getCurrentDroit(date("Y-m-d"));
        } catch (Exception $e) {
            $this->cvo = null;
        }
    }

    public function executeIndex() {
        $this->produits = ConfigurationClient::getCurrent()->declaration->getProduitsWithCVONeg();
    }

}