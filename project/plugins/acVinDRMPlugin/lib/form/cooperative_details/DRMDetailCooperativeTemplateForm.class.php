<?php

class DRMDetailCooperativeTemplateForm extends DRMDetailCooperativeForm {
    
    public function __construct(acCouchdbJson $details, $defaults = array(), $options = array(), $CSRFSecret = null) {
        parent::__construct($details, $defaults, $options, $CSRFSecret);
        unset($this['_revision']);
    }


    public function configure() {
        $item_form_class = $this->getFormItemClass();
        $this->embedForm('var---nbItem---', new $item_form_class($this->details->addDetail()));
        $this->widgetSchema->setNameFormat(sprintf("%s[%%s]", $this->getFormName()));
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }
    
    public function getFormTemplate() {
        
        return $this['var---nbItem---'];
    }
    
}