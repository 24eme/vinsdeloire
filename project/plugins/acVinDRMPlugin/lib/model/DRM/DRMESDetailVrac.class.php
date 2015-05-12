<?php
/**
 * Model for DRMESDetailVrac
 *
 */

class DRMESDetailVrac extends BaseDRMESDetailVrac {

    protected $vrac = null;

    public function getProduitDetail() {
        
        return $this->getParent()->getProduitDetail();
    }

    public function getVrac() {
        if (is_null($this->vrac)) {
            $this->vrac = VracClient::getInstance()->find($this->identifiant);
        }

        return $this->vrac;
    }

    public function getIdentifiantLibelle() {

        return $this->getVrac()->getNumeroArchive();
    }
}
