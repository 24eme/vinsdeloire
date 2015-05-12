<?php
/**
 * Model for DRMDeclaratif
 *
 */

class DRMDeclaratif extends BaseDRMDeclaratif {

    public function init($params = array()) {
        parent::init($params);
        $keepStock = isset($params['keepStock']) ? $params['keepStock'] : true;
        if (!$keepStock) {
            $this->adhesion_emcs_gamma = null;
            $this->paiement->douane->frequence = null;
            $this->paiement->douane->moyen = null;
            $this->paiement->cvo->frequence = null;
            $this->paiement->cvo->moyen = null;
            $this->caution->dispense = null;
            $this->caution->organisme = null;
        }
        $this->defaut_apurement = null;
        $this->daa->debut = null;
        $this->daa->fin = null;
        $this->dsa->debut = null;
        $this->dsa->fin = null;
    }

    public function hasApurementPossible() {
        if (
            $this->daa->debut ||
            $this->daa->fin ||
            $this->dsa->debut ||
            $this->dsa->debut ||
            $this->adhesion_emcs_gamma
        ) {
            
            return true;
        } else {
            
            return false;
        }
    }
}