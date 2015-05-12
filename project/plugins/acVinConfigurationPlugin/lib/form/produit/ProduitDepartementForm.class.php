<?php
class ProduitDepartementForm extends sfForm {

    public function configure() {
    	$this->setWidgets(array(
    		'departement' => new sfWidgetFormInputText()
    	));
    	$this->setValidators(array(
    		'departement' => new sfValidatorString(array('required' => false))
    	));
		$this->widgetSchema->setLabels(array(
			'departement' => 'Département: '
		));
    	
    	$this->setDefaults(array(
    		'departement' => $this->getOption('departement')
    	));
        $this->widgetSchema->setNameFormat('produit_departement[%s]');
    }
}