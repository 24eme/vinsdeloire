<?php

class VracEtablissementCampagneForm extends sfForm {

    public function __construct($identifiantEtablissement, $defaultCampagne) {
        $this->etablissement_id = $identifiantEtablissement;
        $this->default_campagne = $defaultCampagne;
        return parent::__construct();
    }

    public function configure() {
        $list = VracClient::getInstance()->listCampagneByEtablissementId($this->etablissement_id);
        $this->setWidgets(array(
            'campagne' => new sfWidgetFormChoice(array('choices' => $list, 'default' => $this->default_campagne))
        ));
        $this->setValidators(array(
            'campagne' => new sfValidatorChoice(array('required' => true, 'choices' => $list))
        ));
        $this->widgetSchema->setLabels(array(
            'campagne' => 'Campagne Viticole'
        ));
        $this->widgetSchema->setNameFormat('etablissementcampagne[%s]');
    }

}