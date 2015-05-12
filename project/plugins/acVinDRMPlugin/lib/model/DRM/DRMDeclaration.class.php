<?php
/**
 * Model for DRMDeclaration
 *
 */

class DRMDeclaration extends BaseDRMDeclaration {

	public function getChildrenNode() {

		return $this->certifications;
	}

    public function getMouvements() {
        $produits = $this->getProduitsDetails();
        $mouvements = array();
        foreach($produits as $produit) {
            $mouvements = array_replace_recursive($mouvements, $produit->getMouvements());
        }

        return $mouvements;
    }

    public function cleanDetails() {
        $delete = false;
        foreach($this->getProduitsDetails() as $detail) {
            if ($detail->isSupprimable()) {
                $detail->delete();
                $delete = true;
            }
        }

        if($delete) {
           $this->cleanNoeuds();
        }
    }

    public function cleanNoeuds() {
        $this->_cleanNoeuds();
    }
    
    public function hasProduitDetailsWithStockNegatif() {
       foreach ($this->getProduitsDetails() as $prod) {
            if ($prod->hasProduitDetailsWithStockNegatif()) {
                return true;
            }
        }        
        return false;
    }
    
}