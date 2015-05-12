<?php
class ProduitDefinitionValidatorSchema extends sfValidatorSchema
{
	
	protected  $_object;
	
    public function configure($options = array(), $messages = array()) {
        $this->addMessage('indisponible', "Ce code existe déjà pour ce noeud");
    }

  public function __construct($object, $fields = null, $options = array(), $messages = array())
  {
    $this->_object = $object;
    parent::__construct($fields, $options, $messages);
  }
  protected function doClean($values)
  { 
  	if (isset($values['code']) && !empty($values['code'])) {
  		if ($this->_object->getKey() == Configuration::DEFAULT_KEY && $this->_object->getParent()->exist($values['code'])) {
  			throw new sfValidatorErrorSchema($this, array($this->getOption('code') => new sfValidatorError($this, 'indisponible')));
  		}
  	}
  	if (isset($values['secteurs'])) {
	    foreach($values['secteurs'] as $key => $value)
	    {
	      if (!$value['departement'])
	      {
	        unset($values['secteurs'][$key]);
	      }
	    }
  	}
  	if (isset($values['droit_douane'])) {
	    foreach($values['droit_douane'] as $key => $value)
	    {
	      if (!$value['date'] && !$value['taux'])
	      {
	        unset($values['droit_douane'][$key]);
	      }
	    }
  	}
  	if (isset($values['droit_cvo'])) {
	    foreach($values['droit_cvo'] as $key => $value)
	    {
	      if (!$value['date'] && !$value['taux'])
	      {
	        unset($values['droit_cvo'][$key]);
	      }
	    }
  	}
  	if (isset($values['labels'])) {
	    foreach($values['labels'] as $key => $value)
	    {
	      if (!$value['label'] && !$value['code'])
	      {
	        unset($values['labels'][$key]);
	      }
	    }
  	}
    return $values;
  }
}