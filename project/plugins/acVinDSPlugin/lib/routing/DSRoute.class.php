<?php
class DSRoute extends sfObjectRoute implements InterfaceEtablissementRoute {

	protected $ds = null;

	protected function getObjectForParameters($parameters) {

        if (preg_match('/^[0-9]{4}[0-9]{2}$/',$parameters['periode'])) {            
            $periode = $parameters['periode'];
        } else {
            throw new InvalidArgumentException(sprintf('The "%s" route has an invalid parameter "%s" value "%s".', $this->pattern, 'periode', $parameters['periode']));
        }
        
        if (preg_match('/^[0-9]{8}$/',$parameters['identifiant'])) {            
            $identifiant = $parameters['identifiant'];
        } else {
            throw new InvalidArgumentException(sprintf('The "%s" route has an invalid parameter "%s" value "%s".', $this->pattern, 'identifiant', $parameters['identifiant']));
        }

        
        $this->ds = DSClient::getInstance()->findByIdentifiantAndPeriode($identifiant, $periode);
        if (!$this->ds) {
            throw new sfError404Exception(sprintf('No DS found with the id "%s" and the periode "%s".',  $parameters['identifiant'],$parameters['periode']));
        }
        return $this->ds;
    }

    protected function doConvertObjectToArray($object) {  
        $parameters = array("identifiant" => $object->identifiant, "periode" => $object->periode);
        return $parameters;
    }

    public function getDS() {
        if (!$this->ds) {
            $this->getObject();
        }

        return $this->ds;
    }

    public function getEtablissement() {

        return $this->getDS()->getEtablissementObject();
    }
}