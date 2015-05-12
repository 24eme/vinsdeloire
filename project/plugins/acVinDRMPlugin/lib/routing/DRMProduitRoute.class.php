<?php

class DRMProduitRoute extends DRMRoute {
    
    protected function getObjectForParameters($parameters) {
        parent::getObjectForParameters($parameters);

        return $this->getDRM()->produits->get($parameters['certification'])->get($parameters['genre'])->get($parameters['appellation'])->get($parameters['indice']);
    }

    protected function doConvertObjectToArray($object) {
        $parameters = parent::doConvertObjectToArray($object->getDocument());

        $parameters['certification'] = $object->getCertification()->getKey();
        $parameters['genre'] = $object->getGenre()->getKey();
        $parameters['appellation'] = $object->getAppellation()->getKey();
        $parameters['indice'] = $object->getKey();
        
        return $parameters;
    }
    
}