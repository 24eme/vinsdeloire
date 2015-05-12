<?php

class DRMCompteLoginValidator extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->setMessage('invalid', "Ce login n'existe pas");
    }

    protected function doClean($values) {
        if (!$values['login']) {
            return array_merge($values);
        }
        
        $compte = acCouchdbManager::getClient('_Compte')->retrieveByLogin($values['login']);

        if (!$compte) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('login') => new sfValidatorError($this, 'invalid')));
        }
        
        if ($compte->getType() != 'CompteTiers') {
            throw new sfValidatorErrorSchema($this, array($this->getOption('login') => new sfValidatorError($this, 'invalid')));
        }
            
        return array_merge($values, array('compte' => $compte));
    }

}
