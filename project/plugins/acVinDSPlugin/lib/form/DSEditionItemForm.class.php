<?php
class DSEditionItemForm extends acCouchdbObjectForm {

    protected $declaration = null;
    
    public function __construct(acCouchdbJson $declaration, $options = array(), $CSRFSecret = null) {
        $this->declaration = $declaration;
        parent::__construct($declaration, $options, $CSRFSecret);
    }
  
    public function configure() {

        
        $this->setWidget('stock_declare', new sfWidgetFormInput());    

        $this->widgetSchema->setLabels(array(
            'stock_declare' => 'Volume Stock'
        ));

        $this->setValidators(array(
            'stock_declare' => new sfValidatorNumber(array('required' => false))
        ));
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);  
    }
    
    public function getDeclaration()
        {
        return $this->declaration;
        }
        
    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}

