<?php

class DRMDetailCooperativeItemForm extends DRMESDetailsItemForm {

    public function configure() {
        parent::configure();
        $this->setWidget('identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' => array(EtablissementFamilles::PSEUDOFAMILLE_COOPERATIVE))));
        $this->setValidator('identifiant', new ValidatorEtablissement(array('required' => false)));
    }

    public function getFormName() {

        return "drm_detail_cooperative_item";
    }

    public function getIdentifiantChoices() {
        
        return array();
    }

    public function getPostValidatorClass() {

        return 'DRMDetailCooperativeItemValidator';
    }

}