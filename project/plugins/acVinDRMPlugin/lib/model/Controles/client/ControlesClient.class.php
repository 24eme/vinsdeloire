<?php

class ControlesClient extends acCouchdbClient {
  public static $controles = null;
  
	public static function getInstance() {
      return acCouchdbManager::getClient("Controles");
    }

  public function retrieveControles() {
    if (!self::$controles)
      self::$controles = parent::retrieveDocumentById('CONTROLES');
    return self::$controles;
  }
  
  public function findAll() {
  	$controles = $this->retrieveControles();
  	unset($controles['_id']);
  	unset($controles['_rev']);
  	unset($controles['type']);
  	return $controles;
  }

  public function getMessage($id) {
    try {
      return $this->retrieveControles()->{$id};
    }catch(Exception $e) {
      return "\"".$id."\" PAS DE CONTROLE TROUVÉ !!";
    }
  }
}
