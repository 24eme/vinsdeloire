<?php
/**
 * Model for DRMMention
 *
 */

class DRMMention extends BaseDRMMention {
	   
    public function getChildrenNode() {

        return $this->lieux;
    }

    public function getAppellation() {

        return $this->getParentNode();
    }

    public function getCertification() {
        
        return $this->getAppellation()->getCertification();
    }

    public function getLieuxArray() {
        $lieux = array();
        foreach($this->lieux as $lieu) {
            $lieux[$lieu->getHash()] = $lieu;
        } 

        return $lieux;
    }
}