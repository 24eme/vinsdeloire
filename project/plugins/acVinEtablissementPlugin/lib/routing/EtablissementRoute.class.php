<?php

class EtablissementRoute extends sfObjectRoute implements InterfaceEtablissementRoute {

    protected $etablissement = null;
    
    protected function getObjectForParameters($parameters = null) {
      $this->etablissement = EtablissementClient::getInstance()->find($parameters['identifiant']);
      return $this->etablissement;
    }

    protected function doConvertObjectToArray($object = null) {
  
        return array("identifiant" => $object->getIdentifiant());
    }

    public function getEtablissement() {
      if (!$this->etablissement) {
           $this->etablissement = $this->getObject();
      }
      return $this->etablissement;
    }
}
