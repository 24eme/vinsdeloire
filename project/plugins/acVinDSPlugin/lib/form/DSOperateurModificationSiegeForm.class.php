<?php

class DSOperateurModificationSiegeForm extends acCouchdbObjectForm {

    public function configure()
    {
        $this->setWidget('adresse', new sfWidgetFormInput());        
        $this->setWidget('code_postal', new sfWidgetFormInput());   
        $this->setWidget('commune', new sfWidgetFormInput());

        $this->widgetSchema->setLabels(array(
            'adresse' => 'Adresse',
            'code_postal' => 'CP*',
            'commune' => 'Ville*',
        ));

        $this->setValidators(array(
            'adresse' => new sfValidatorString(array('required' => false)),
            'code_postal' => new sfValidatorString(array('required' => true, 'min_length' => 5,'max_length' => 5)),
            'commune' => new sfValidatorString(array('required' => true)),
        ));
    }

}