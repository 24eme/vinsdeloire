<?php

class SV12AddProduitValidator extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) { 
    }

    protected function doClean($values) {
      if (isset($values['withviti']) && $values['withviti']) {
	if (!$values['identifiant'])
	  throw new sfValidatorErrorSchema($this, array('identifiant' => new sfValidatorError($this, 'required'))); 
	if (!$values['raisinetmout'])
	  throw new sfValidatorErrorSchema($this, array('raisinetmout' => new sfValidatorError($this, 'required'))); 
      }
      return $values;
    }
}