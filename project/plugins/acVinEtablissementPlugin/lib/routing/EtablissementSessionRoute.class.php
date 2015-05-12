<?php

class EtablissementSessionRoute extends sfObjectRoute {

    protected $drm = null;
    
    protected function getObjectForParameters($parameters = null) {
	   return $this->getEtablissement();
    }

    protected function doConvertObjectToArray($object = null) {
        $parameters = array("identifiant" => $this->getEtablissement()->getIdentifiant());
        return $parameters;
    }

    public function getEtablissement() {
	   return sfContext::getInstance()->getUser()->getEtablissement();
    }
}
