<?php

abstract class DRMESDetailsItemForm extends acCouchdbObjectForm {

    public function __construct(acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        parent::__construct($object, $options, $CSRFSecret);
    }
  
    public function configure() {

        $this->setWidget('identifiant', new sfWidgetFormChoice(array('choices' =>  $this->getIdentifiantChoices()), array('class' => 'autocomplete')));
        $this->setWidget('volume', new sfWidgetFormInputFloat(array(), array('autocomplete' => 'off', 'class' => 'num num_float')));
        $this->setWidget('date_enlevement', new sfWidgetFormInput());
        
        $this->setValidator('identifiant', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getIdentifiantChoices()))));
        $this->setValidator('volume', new sfValidatorNumber(array('required' => false, 'min' => 0), array('min' => "La saisie d'un nombre négatif est interdite")));
        $this->setValidator('date_enlevement', new sfValidatorDate(array('required' => false, 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~',
                                                                         'date_output' => 'Y-m-d')));

        $post_validator_class = $this->getPostValidatorClass();
        $this->validatorSchema->setPostValidator(new $post_validator_class()); 
        $this->widgetSchema->setNameFormat(sprintf("%s[%%s]", $this->getFormName()));
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }
    
    public function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        if(!$this->getObject()->date_enlevement) $this->setDefault('date_enlevement', $this->getObject()->getDocument()->getDate());

        $date = new DateTime($this->getDefault('date_enlevement'));
        $this->setDefault('date_enlevement', $date->format('d/m/Y'));
    }
    
    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
    
    public function getProduitDetail() {
        
        return $this->getObject()->getProduitDetail();
    }

    public abstract function getFormName();

    public abstract function getIdentifiantChoices();


    public function getPostValidatorClass() {

        return 'DRMDetailItemValidator';
    }
}
