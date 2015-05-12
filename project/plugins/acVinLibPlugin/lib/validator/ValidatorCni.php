<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class ValidatorCNI
 * @author mathurin
 */
class ValidatorCni extends sfValidatorRegex {

  public function configure($options = array(),$messages = array())
  {
    parent::configure($options, $messages);
    $this->addOption('pattern', '/^[A-Z0-9]{12}$/');
    if(empty($messages)) $this->messages = array('invalid' => 'Le numéro de Cni doit contenir 12 caractères (chiffres et lettres majuscules)');
  }
    
}

