<?php
class ProduitDetailsForm extends acCouchdbObjectForm {

    public function configure() { 
    	$this->embedForm(
			'entrees', 
			new ProduitTypeDetailForm($this->getObject()->getOrAdd('entrees'), array('libelle' => 'Entrée'))
		);
		$this->embedForm(
			'sorties', 
			new ProduitTypeDetailForm($this->getObject()->getOrAdd('sorties'), array('libelle' => 'Sortie'))
		);
    	$this->widgetSchema->setNameFormat('[%s]');
    }
}