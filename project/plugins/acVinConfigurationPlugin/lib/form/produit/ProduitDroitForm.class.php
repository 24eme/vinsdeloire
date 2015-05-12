<?php
class ProduitDroitForm extends sfForm {

    public function configure() {
    	$this->setWidgets(array(
			'date' => new sfWidgetFormInputText( array('default' => ''), array('class' => 'hasDatepicker') ),
			'taux' => new sfWidgetFormInputFloat()  		
    	));
		$this->widgetSchema->setLabels(array(
			'date' => 'Date: ',
			'taux' => 'Taux: '
		));
                
               
		$this->setValidators(array(
			'date' => new sfValidatorString(array('required' => false)),
			'taux' => new sfValidatorNumber(array('required' => false))
		));
		if ($droit = $this->getOption('droit')) {
			$date = new DateTime($droit->date);
			$this->setDefaults(array(
	    		'date' => $date->format('d/m/Y'),
	    		'taux' => $droit->taux
	    	));
		}		
        $this->widgetSchema->setNameFormat('produit_droit[%s]');
    }
}