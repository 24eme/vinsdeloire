<?php

class VracRoute extends sfObjectRoute {

    protected $vrac = null;
    
    protected function getObjectForParameters($parameters) {
        
        if (preg_match('/^[0-9]+$/', $parameters['numero_contrat'])) {
            $numero_contrat = $parameters['numero_contrat']; 
         } else {
            throw new InvalidArgumentException(sprintf('The "%s" route has an invalid parameter "%s" value "%s".', $this->pattern, 'numero_contrat', $parameters['numero_contrat']));
        }
        $this->vrac = VracClient::getInstance()->findByNumContrat($numero_contrat);
        if (!$this->vrac) 
        {
            throw new sfError404Exception(sprintf('No contrat found with numero "%s".',  $parameters['numero_contrat']));
        }
        
        return $this->vrac;
    }

    protected function doConvertObjectToArray($object) {  
        $parameters = array("numero_contrat" => $object->numero_contrat);
        
        return $parameters;
    }

    
    public function getVrac() {
        if (!$this->vrac) {
            $this->getObject();
        }

        return $this->vrac;
    }
    
}