<?php
/**
 * Model for ConfigurationDroit
 *
 */

class ConfigurationDroit extends BaseConfigurationDroit {

    public function getNoeud() {

        return $this->getParent()->getNoeud();
    }
}