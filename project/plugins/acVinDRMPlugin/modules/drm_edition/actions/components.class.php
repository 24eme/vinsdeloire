<?php

class drm_editionComponents extends sfComponents {
    
    public function executeItemForm() {
        if (is_null($this->form)) {
            $this->form = new DRMDetailForm($this->detail);
        }
    }

    public function executeProduitForm() {
    	$this->form = new DRMProduitForm($this->drm, $this->config);
    }
}
