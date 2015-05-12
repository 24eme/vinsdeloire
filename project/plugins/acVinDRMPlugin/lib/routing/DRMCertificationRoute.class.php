<?php

class DRMCertificationRoute extends DRMRoute {
    
    public function getConfigCertification() {
        
        return $this->getDRMCertification()->getConfig();
    }
    
    public function getDRMCertification() {
        
        return $this->getObject();
    }
    
    protected function getObjectForParameters($parameters) {
        parent::getObjectForParameters($parameters);

        if (!array_key_exists('certification', $parameters)) {
            return $this->getDRM()->declaration->certifications->getFirst();
        }
        
        if ($this->getDRMConfiguration()->declaration->certifications->exist($parameters['certification'])) {
            if (isset($this->options['add_noeud']) && $this->options['add_noeud'] === true) {
                return $this->getDRM()->declaration->certifications->add($parameters['certification']);
            } else {
                return $this->getDRM()->declaration->certifications->get($parameters['certification']);
            }
        }
        
        return null;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = parent::doConvertObjectToArray($object->getDocument());
        $parameters["certification"] = $object->getKey();
        
        return $parameters;
    }

    public function getChildrenNode() {

        return $this->genres;
    }

}