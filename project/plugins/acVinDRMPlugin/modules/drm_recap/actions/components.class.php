<?php

class drm_recapComponents extends sfComponents {
    
    public function executeItemForm() {
        if (is_null($this->form)) {
            $this->form = new DRMDetailForm($this->detail);
        }
    }
    
    public function executeOnglets() {
        $this->items = $this->drm_lieu->getCertification()->getLieuxArray();
    }

    public function executeProduitForm() {
    	$this->form = new DRMProduitForm($this->drm, $this->config);
    }
}
