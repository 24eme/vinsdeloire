<?php

class DRMDetailRoute extends DRMLieuRoute {

    public function getDRMDetail() {
        
        return $this->getObject();
    }
    
    public function getDRMLieu() {

        return $this->getDRMDetail()->getLieu();
    }
    
    protected function getObjectForParameters($parameters) {
        $config_lieu = parent::getObjectForParameters($parameters);
        
        $drm_detail = $this->getDRM()->get($config_lieu->getHash())
                                     ->couleurs->add($parameters['couleur'])
                                     ->cepages->add($parameters['cepage'])
                                     ->details->get($parameters['detail']);

        return $drm_detail;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = parent::doConvertObjectToArray($object->getLieu());
        $parameters['couleur'] = $object->getCouleur()->getKey();
        $parameters['cepage'] = $object->getCepage()->getKey();
        $parameters['detail'] = $object->getKey();
        
        return $parameters;
    }

}