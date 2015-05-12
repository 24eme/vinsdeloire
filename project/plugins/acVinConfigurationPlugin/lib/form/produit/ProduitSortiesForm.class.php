<?php
class ProduitSortiesForm extends acCouchdbObjectForm {

	public function configure() {    	
    	$this->embedForm(
				'repli', 
				new ProduitDetailForm($this->getObject()->getOrAdd('repli'), array('libelle' => 'Entrée repli:'))
		);
		$this->embedForm(
				'declassement', 
				new ProduitDetailForm($this->getObject()->getOrAdd('declassement'), array('libelle' => 'Entrée repli:'))
		);
    	$this->widgetSchema->setNameFormat('sorties[%s]');
    }
}