<?php
/**
 * Model for DRMESDetailExport
 *
 */

class DRMESDetailExport extends BaseDRMESDetailExport {
    public function getProduitDetail() {
        
        return $this->getParent()->getProduitDetail();
    }

    public function getIdentifiantLibelle() {

        return ConfigurationClient::getInstance()->getCountry($this->identifiant);
    }
}
