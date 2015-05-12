<?php

class VracVolumeEnleveForm extends acCouchdbObjectForm {
     
    public function configure()
    {
        $this->setWidget('volume_enleve', new sfWidgetFormInputFloat());
        
        $this->widgetSchema->setLabels(array(
            'volume_enleve' => 'Saisir le volume enlevé :'));
        
        $this->setValidators(array(
            'volume_enleve' => new sfValidatorNumber(array('required' => true))));   
               
        $this->widgetSchema->setNameFormat('vrac[%s]');
        
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        
        if($this->getObject()->volume_propose <= $this->getObject()->volume_enleve)
        { 
            $this->getObject()->valide->statut = "SOLDE";
        }
    }
  
}

