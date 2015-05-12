<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneModificationForm
 * @author mathurin
 */
class DSOperateurModificationForm extends acCouchdbObjectForm {
   
    public function configure()
    {      
        $this->configureOperateur();
        $formSiege = new VracSoussigneModificationSiegeForm($this->getObject()->siege);
        $this->embedForm('siege', $formSiege);
        $this->widgetSchema->setNameFormat('ds[%s]');    
    }
    
    private function configureOperateur($label)
    {         
        $this->setWidget('no_accises', new sfWidgetFormInput()); 
        $this->setWidget('no_tva_intracommunautaire', new sfWidgetFormInput());
            
        $this->widgetSchema->setLabels(array(
            'no_accises' => 'N° ACCISE',
            'no_tva_intracommunautaire' => 'TVA Intracomm.'
        ));
                
        $this->setValidators(array(
            'no_accises' => new sfValidatorString(array('required' => false)),
            'no_tva_intracommunautaire' => new sfValidatorString(array('required' => false))
        )); 
    }
}


