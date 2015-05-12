<?php

class DRMDetailVracItemForm extends DRMESDetailsItemForm {

    public function getFormName() {

        return "drm_detail_vrac_item";
    }

    public function getIdentifiantChoices() {
        $vracs = $this->getProduitDetail()->getContratsVrac();
        if($this->getObject()->identifiant && !array_key_exists($this->getObject()->identifiant, $vracs) && $this->getObject()->getVrac()) {
            $vrac = $this->getObject()->getVrac();
            $vracs[$this->getObject()->identifiant] = sprintf("%s - %s - %s hl [%s/%s]", $vrac->acheteur->nom, $vrac->numero_archive, round($vrac->volume_propose - $vrac->volume_enleve, 2), $vrac->volume_enleve, $vrac->volume_propose);
        }
        return array_merge(array("", ""), $vracs);
    }

    public function getPostValidatorClass() {

        return 'DRMDetailVracItemValidator';
    }

}
