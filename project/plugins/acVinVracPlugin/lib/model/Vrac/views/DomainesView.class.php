<?php

class VracDomainesView extends acCouchdbView
{
	const KEY_VENDEURID = 0;
	const KEY_DOMAINE = 1;

    public static function getInstance() {
        return acCouchdbManager::getView('vrac', 'domaines','Vrac');
    }

    public function findDomainesByVendeur($vendeurId) {

    	return $this->client->startkey(array($vendeurId))
                    		->endkey(array($vendeurId, array()))
                                ->group_level(2)
                    		->getView($this->design, $this->view);
    }

}  