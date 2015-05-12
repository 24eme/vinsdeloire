<?php

class SV12Route extends sfObjectRoute implements InterfaceEtablissementRoute {

    protected $sv12 = null;
    
    protected function getObjectForParameters($parameters) {

        $this->sv12 = SV12Client::getInstance()->find('SV12-'.$parameters['identifiant'].'-'.$parameters['periode_version']);

        if (!$this->sv12) {

            throw new sfError404Exception(sprintf("La SV12 n'a pas été trouvée"));
        }

        $control = isset($this->options['control']) ? $this->options['control'] : array();

        if (in_array('valid', $control) && !$this->sv12->isValidee()) {
            
            throw new sfException('La SV12 doit être validée');
        }

        if (in_array('edition', $control) && $this->sv12->isValidee()) {

            throw new sfException('La SV12 ne peut pas être éditée car elle est validée');
        }

        return $this->sv12;
    }

    protected function doConvertObjectToArray($object) {  
        $parameters = array("identifiant" => $object->getIdentifiant(), "periode_version" => $object->getPeriodeAndVersion());
        return $parameters;
    }
    
    public function getSV12() {
        if (!$this->sv12) {
            $this->getObject();
        }

        return $this->sv12;
    }

    public function getEtablissement() {

        return $this->getSV12()->getEtablissementObject();
    }

}