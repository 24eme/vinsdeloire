<?php

class DRMLieuRoute extends DRMCertificationRoute {

    public function getConfigLieu() {
        
        return $this->getDRMLieu()->getConfig();
    }

    public function getDRMLieu() {

        return $this->getObject();
    }

    public function getConfigCertification() {

        return $this->getConfigLieu()->getAppellation()->getGenre()->getCertification();
    }

    protected function getObjectForParameters($parameters) {
        $drm_certification = parent::getObjectForParameters($parameters);

        if (!array_key_exists('appellation', $parameters)) {

        	return $drm_certification->genres->getFirst()->appellations->getFirst()->mentions->getFirst()->lieux->getFirst();
        }

        if (isset($this->options['add_noeud']) && $this->options['add_noeud'] === true) {
            return $drm_certification->genres->add($parameters['genre'])
                                 ->appellations->add($parameters['appellation'])
                                 ->mentions->add($parameters['mention'])
                                 ->lieux->add($parameters['lieu']);
        } else {
            return $drm_certification->genres->get($parameters['genre'])
                                 ->appellations->get($parameters['appellation'])
                                 ->mentions->add($parameters['mention'])
                                 ->lieux->get($parameters['lieu']);
        }

        
    }

    protected function doConvertObjectToArray($object) {
        if ($object->getDefinition()->getHash() == "/declaration/certifications/*/genres/*/appellations/*/mentions/*/lieux/*") {
            $parameters = parent::doConvertObjectToArray($object->getCertification());
            $parameters['genre'] = $object->getAppellation()->getGenre()->getKey();
            $parameters['appellation'] = $object->getAppellation()->getKey();
            $parameters['mention'] = $object->getMention()->getKey();
            $parameters['lieu'] = $object->getKey();
        } elseif($object->getDefinition()->getHash() == "/declaration/certifications/*") {
            $parameters = parent::doConvertObjectToArray($object);
        }

        return $parameters;
    }

}