<?php
class ProduitTypeDetailForm extends acCouchdbObjectForm {

    public function configure() { 
    	$this->embedForm(
				'repli', 
				new ProduitDetailForm($this->getObject()->getOrAdd('repli'), array('libelle' => $this->getOption('libelle', null).' repli: '))
		);
    	$this->embedForm(
				'declassement', 
				new ProduitDetailForm($this->getObject()->getOrAdd('declassement'), array('libelle' => $this->getOption('libelle', null).' déclassement: '))
		);
    	$this->widgetSchema->setNameFormat('[%s]');
    }
}