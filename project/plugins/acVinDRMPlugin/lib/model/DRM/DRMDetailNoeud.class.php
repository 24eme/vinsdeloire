<?php
/**
 * Model for DRMDetailNoeud
 *
 */

class DRMDetailNoeud extends BaseDRMDetailNoeud {

  public function getConfig() {
 
    return $this->getParent()->getConfig()->get($this->getKey());
  }
  
  protected function init($params = array()) {
      parent::init($params);
   
	   $keepStock = isset($params['keepStock']) ? $params['keepStock'] : true;
	  
      foreach ($this as $key => $entree) {
      	if ($this->getKey() == 'stocks_fin' && $keepStock) {
	   		  $this->getParent()->stocks_debut->set($key, $this->get($key));
	  	  } 
      	
        $this->set($key, null);
      }
    }

  public function set($key, $value) {
    if (is_object($value)) {
        parent::set($key, $value);
        return ;
    }

    if (!$this->getConfig()->exist($key)) {
      return;
    }
      
    if (!$this->getConfig()->exist($key) && !$this->getConfig()->get($key)->isWritable()) {
      
      throw new sfException("$key is not writable");
    }

    if ($key == 'vrac' && !$value) {
      $this->getParent()->remove('vrac');
    }
    parent::set($key, $value);
  }

  public function isModifiedMother($key) {

    return $this->getDocument()->isModifiedMother($this->getHash(), $key);
  }

}