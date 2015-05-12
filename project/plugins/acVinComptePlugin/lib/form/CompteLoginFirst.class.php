<?php
class CompteLoginFirstForm extends BaseForm {
    public function configure() {

        $this->setWidgets(array(
                'login'   => new sfWidgetFormInputText(),
                'mdp'   => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'login'  => 'Identifiant : ',
                'mdp'  => 'Code de création : '
        ));

        $this->setValidators(array(
                'login' => new sfValidatorString(array('required' => true)),
                'mdp' => new sfValidatorString(array('required' => true, 'min_length' => 4)),
        ));
        
        $this->widgetSchema->setNameFormat('first_connection[%s]');

        $this->validatorSchema['login']->setMessage('required', 'Champ obligatoire');
        $this->validatorSchema['mdp']->setMessage('required', 'Champ obligatoire');
        

        $this->validatorSchema->setPostValidator(new ValidatorCompteLoginFirst());
    }
}
