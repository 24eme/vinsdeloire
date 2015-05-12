<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class ValidatorSiret
 * @author mathurin
 */
class ValidatorSiret extends sfValidatorRegex {
    
  public function configure($options = array(),$messages = array())
  {
    parent::configure($options, $messages);
    $this->addOption('pattern', '/^\d{14}$/');
    if(empty($messages)) $this->messages = array('invalid' => 'Le numéro de Siret doit contenir 14 chiffres');
  }
    
}

