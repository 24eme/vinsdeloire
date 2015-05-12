<?php

class DRMCampagneValidator extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->addMessage('impossible', "Vous ne pouvez pas créer une DRM future.");
        $this->setMessage('invalid', "Cette DRM existe déjà.");
        $this->setMessage('required', "Champs obligatoires.");
    }

    protected function doClean($values) {
        if (!$values['months']) {
        	throw new sfValidatorErrorSchema($this, array($this->getOption('months') => new sfValidatorError($this, 'required')));
        }
        if (!$values['years']) {
        	throw new sfValidatorErrorSchema($this, array($this->getOption('years') => new sfValidatorError($this, 'required')));
        }
        $periode = sprintf('%04d%02d', $values['years'], $values['months']);
        if ($periode > date('Ym')) {
        	throw new sfValidatorErrorSchema($this, array($this->getOption('years') => new sfValidatorError($this, 'impossible')));
        }
        $periode = sprintf('%04d-%02d', $values['years'], $values['months']);
        $tiers = sfContext::getInstance()->getUser()->getTiers();
        
        $drm = DRMClient::getInstance()->findByIdentifiantPeriodeAndRectificative($tiers->identifiant, $periode);
		
        if ($drm) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('months') => new sfValidatorError($this, 'invalid')));
        }       
        $values['periode'] = $periode;
        return $values;
    }

}
