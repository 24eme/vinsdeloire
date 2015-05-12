<?php

class CompteTeledeclarantForm extends acCouchdbObjectForm 
{
    public function configure() 
    {
        $this->setWidgets(array(
                'email' => new sfWidgetFormInputText(),
                'mdp1'  => new sfWidgetFormInputPassword(),
                'mdp2'  => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'email' => 'Adresse e-mail*: ',
                'mdp1'  => 'Mot de passe*: ',
                'mdp2'  => 'Vérification du mot de passe*: '
        ));

        $this->widgetSchema->setNameFormat('ac_vin_compte[%s]');

        $this->setValidators(array(
                'email' => new sfValidatorEmail(array('required' => true),array('required' => 'Champ obligatoire', 'invalid' => 'Adresse email invalide.')),
                'mdp1'  => new sfValidatorString(array('required' => false, "min_length" => "8"), array('required' => 'Champ obligatoire', "min_length" => "Votre mot de passe doit comporter au minimum 8 caractères.")),
                'mdp2'  => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')),
        ));
        
        $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('mdp1', 
                                                                             sfValidatorSchemaCompare::EQUAL, 
                                                                             'mdp2',
                                                                             array(),
                                                                             array('invalid' => 'Les mots de passe doivent être identique.')));
    }

    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        
        if($this->getValue('mdp1')) {
            $this->getObject()->setMotDePasseSSHA($this->getValue('mdp1'));
        }
    }
}