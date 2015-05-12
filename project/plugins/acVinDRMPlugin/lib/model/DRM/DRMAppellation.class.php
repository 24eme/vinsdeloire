<?php
/**
 * Model for DRMCertification
 */

class DRMAppellation extends BaseDRMAppellation {
  
    public function getChildrenNode() {

        return $this->mentions;
    }

    public function getGenre() {

        return $this->getParentNode();
    }

    public function getCertification() {
        
        return $this->getGenre()->getParent()->getParent();
    }
    
    public function updateDroits($droits) {
    	  $merge = array();
    	  foreach ($this->getDroits() as $typedroits => $droit) {
    		      $droits->add($typedroits)->add($droit->code)->integreVolume($this->sommeLignes(DRMDroits::getDroitSorties($merge)), $this->sommeLignes(DRMDroits::getDroitEntrees()), $droit->taux, $this->getReportByDroit($droit), $droit->libelle);
    	  }
    }

    public function getReportByDroit($droit) {
    	$drmPrecedente = $this->getDocument()->getPrecedente();
    	if ($drmPrecedente->isNew()) {
    		  
          return 0;
    	} else {
    		if ($drmPrecedente->get('droits')->get('douane')->exist($droit->code)) {
			    
                return $drmPrecedente->get('droits')->get('douane')->get($droit->code)->cumul;
    		} else {
    			 
                return 0;
    		}
    	}
    }

    public function getDroit($type) {
        
        return $this->getConfig()->getDroits($this->getInterproKey())->get($type)->getCurrentDroit($this->getDocument()->getDate());
    }

    public function getDroits() {
        $conf = $this->getConfig();
        $droits = array();
        foreach ($conf->getDroits($this->getInterproKey()) as $key => $droit) {
	        $droits[$key] = $droit->getCurrentDroit($this->getDocument()->getDate());
        }

        return $droits;
    }

    public function getInterproKey() {

        return $this->getDocument()->getInterpro()->get('_id');
    }

}