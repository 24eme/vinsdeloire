<?php

/**
 * Model for ConfigurationDeclaration
 *
 */
class ConfigurationDeclaration extends BaseConfigurationDeclaration {

    const TYPE_NOEUD = 'declaration';

    public function getChildrenNode() {

        return $this->certifications;
    }

    public function setDonneesCsv($datas) {
        
    }
    
    public function hasDroits() {

        return false;
    }

    public function getTypeNoeud() {

        return self::TYPE_NOEUD;
    }

    public function getDensite() {
        if (!$this->exist('densite') || !$this->_get('densite')) {

            return $this->getParentNode()->getDensite();
        }

        return $this->_get('densite');
    }

    public function getFormatLibelle() {
       
       return "%g% %a% %m% %l% %co% %ce%"; 
    }

    public function getLibelles() {

        return null;
    }

    public function getCodes() {

        return null;
    }

}