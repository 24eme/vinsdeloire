<?php
class AnnuaireAjoutCommercialForm extends acCouchdbObjectForm 
{
	public function configure()
    {
        $this->setWidgets(array(
        	'identite' => new sfWidgetFormInputText(),
            'email' => new sfWidgetFormInputText(),
        	'telephone' => new sfWidgetFormInputText(),
    	));
        $this->widgetSchema->setLabels(array(
        	'identite' => 'Identité*:',
            'email' => 'Email:',
        	'telephone' => 'Téléphone:',
        ));
        $this->setValidators(array(
        	'identite' => new sfValidatorString(array('required' => true)),
        	'email' => new sfValidatorEmail(array('required' => false)),
            'telephone' => new sfValidatorRegex(array(
                                                    'pattern' => "/^\d{10}$/", 
                                                    'required' => false, 
                                                    'max_length' => 10, 
                                                    'min_length' => 10,
                                                    ), 
                                                array(
                                                    'max_length' => 'Le numéro doit être formatté avec 10 chiffres',
                                                    'min_length' => 'Le numéro doit être formaté avec 10 chiffres',
                                                    'invalid' => 'Ne doit contenir que des chiffres',
                                                )),
        ));
  		$this->widgetSchema->setNameFormat('annuaire_ajout_commercial[%s]');
    }
    
    public function doUpdateObject($values) 
    {   
        $value = sprintf("%s (%s)", $values['email'], $values['telephone']);
        $value = preg_replace("/[ ]*\(\)/", "", $value);
        $values['contact'] = $value;
        $this->getObject()->get('commerciaux')->add($values['identite'], $values['contact']);
    }
}