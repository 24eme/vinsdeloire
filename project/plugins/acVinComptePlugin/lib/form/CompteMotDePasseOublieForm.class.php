<?php

class CompteMotDePasseOublieForm extends BaseForm {

    public function setup() {
        $this->setWidgets(array(
            'login' => new sfWidgetFormInputText(array('label' => 'Identifiant :'))
        ));

        $this->setValidators(array(
            'login' => new sfValidatorString(array('required' => true)),
        ));
        
        $this->validatorSchema['login']->setMessage('required', 'Champ obligatoire');

        $this->validatorSchema->setPostValidator(new ValidatorCompteMotDePasseOublie());

        $this->widgetSchema->setNameFormat('mot_de_passe_oublie[%s]');
    }

    public function save() {
        if ($this->isValid()) {
            $compte = $this->getValue('compte');
            $compte->mot_de_passe = "{OUBLIE}" . sprintf("%04d", rand(0, 9999));
            $compte->save();
        } else {
            throw new sfException("form must be valid");
        }
        
        return $compte;
    }
}
