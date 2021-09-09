<?php

/**
 * Model for DRMCepage
 *
 */
class DRMCepage extends BaseDRMCepage {

    public function getChildrenNode() {

        return $this->filter('^details');
    }

    public function getCouleur() {

        return $this->getParentNode();
    }

    public function getProduits() {

        return array($this->getHash() => $this);
    }

    public function getDetailsNoeud($detailsKey) {
        if($detailsKey != DRM::DETAILS_KEY_ACQUITTE && $detailsKey != DRM::DETAILS_KEY_SUSPENDU) {

            throw new sfException(sprintf("La clé détail %s n'est pas autorisé", $detailsKey));
        }

        return $this->get($detailsKey);
    }


    public function addDetailsNoeud($detailsKey) {
        if($detailsKey != DRM::DETAILS_KEY_ACQUITTE && $detailsKey != DRM::DETAILS_KEY_SUSPENDU) {

            throw new sfException(sprintf("La clé détail %s n'est pas autorisé", $detailsKey));
        }

        return $this->add($detailsKey);
    }

    public function reorderByConf() {

        return null;
    }

    public function getProduitsDetails($teledeclarationMode = false, $detailsKey = null) {
        $details = array();
        foreach ($this->getChildrenNode() as $key => $items) {
            if(!is_null($detailsKey) && $detailsKey != $key) {
                continue;
            }

            foreach($items as $item) {
                $details[$item->getHash()] = $item;
            }
        }

        return $details;
    }

    public function getInao() {
	$inao = $this->_get('inao');
        if ($inao != $this->getConfig()->getInao()) {
		$inao = $this->getConfig()->getInao();
		$this->setInao($inao);
	}
	return $inao;
    }

    public function hasProduitDetailsWithStockNegatif() {
        foreach ($this->getProduitsDetails() as $detail) {
            if ($detail->total < 0) {
                return true;
            }
        }

        return false;
    }

    public function getLieuxArray() {

        throw new sfException('this function need to call before lieu tree');
    }

    public function cleanNoeuds() {
        $nbdetailsACQUITTE = ($this->exist('detailsACQUITTE'))? count($this->detailsACQUITTE) : 0;
        $nbdetails = ($this->exist('details'))? count($this->details) : 0;
        if (($nbdetails+$nbdetailsACQUITTE) == 0 ) {
            return $this;
        }

        return null;
    }

    public function hasRerserveIntepro() {
        return ($this->getDocument()->periode < '202107') && $this->exist('reserve_interpro');
    }

    public function getRerserveIntepro() {
        if ($this->hasRerserveIntepro()) {
            return $this->get('reserve_interpro');
        }
        return 0;
    }
    public function getVolumeCommercialisable() {
        return $this->total - $this->getRerserveIntepro();
    }

}
