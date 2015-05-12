<?php

class CompteRoute extends sfObjectRoute implements InterfaceCompteRoute {

    protected $compte = null;
    
    protected function getObjectForParameters($parameters = null) {
      $this->compte = CompteClient::getInstance()->find(CompteClient::getInstance()->getId($parameters['identifiant']));
      return $this->compte;
    }

    protected function doConvertObjectToArray($object = null) {
      $this->compte = $object;
      return array("identifiant" => $object->getIdentifiant());
    }

    public function getCompte() {
      if (!$this->compte) {
           $this->compte = $this->getObject();
      }
      return $this->compte;
    }
}
