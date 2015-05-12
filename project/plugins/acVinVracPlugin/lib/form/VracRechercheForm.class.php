<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneForm
 * @author mathurin
 */
class VracRechercheForm extends sfForm {     
    
    public function configure()
    {
        $this->setWidget('type', new sfWidgetFormChoice(array('choices' => $this->getTypes(),'expanded' => true)));      
        $this->setWidget('statut',new sfWidgetFormChoice(array('choices' => $this->getStatuts(),'expanded' => true)));     
                
        $this->widgetSchema->setLabels(array(
            'type' => 'Type',
            'statut' => 'Statut'));
        
        $this->setValidators(array(
            'type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getTypes()))),
            'statut' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getStatuts())))));
                
    }
    
    private function getTypes()
    {
        return VracClient::getTypes();
    }
    
    private function getStatuts()
    {
        return VracClient::getStatuts();
    }
}

