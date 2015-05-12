<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracValidationForm
 * @author mathurin
 */
class VracValidationForm extends acCouchdbObjectForm {
   
    
     
    public function configure()
    {
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
    }
    
}

