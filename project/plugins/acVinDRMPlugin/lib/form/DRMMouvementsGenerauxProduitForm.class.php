<?php

class DRMMouvementsGenerauxProduitForm extends acCouchdbObjectForm {

	public function configure() {
        $this->setWidgets(array(
        		'pas_de_mouvement_check' => new WidgetFormInputCheckbox()
        ));
        $this->widgetSchema->setLabels(array(
        		'pas_de_mouvement_check' => 'Pas de mouvement '
        ));

        $this->setValidators(array(
        		'pas_de_mouvement_check' => new ValidatorBoolean(array('required' => false))
        ));


        if ($this->getObject()->hasMouvement()) {
            $this->getWidget('pas_de_mouvement_check')->setAttribute('disabled', 'disabled');
        }

		$this->widgetSchema->setNameFormat('produit_'.$this->getObject()->getKey().'[%s]');
    }
    
    public function doUpdateObject($values) {
    	if (isset($values['pas_de_mouvement_check']) && !$values['pas_de_mouvement_check']) {
    		$values['pas_de_mouvement_check'] = 0;
    	}
        parent::doUpdateObject($values);
    }

}