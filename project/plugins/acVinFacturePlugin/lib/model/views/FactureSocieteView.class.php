<?php

class FactureSocieteView extends FactureEtablissementView
{
    public function findBySociete($societe) {  
      $rows = acCouchdbManager::getClient()
	->startkey(array(0, $societe->identifiant.'00'))
	->endkey(array(0, $societe->identifiant.'99', array()))
	->getView($this->design, $this->view)->rows;
      return array_merge($rows, acCouchdbManager::getClient()
			 ->startkey(array(1, $societe->identifiant.'00'))
			 ->endkey(array(1, $societe->identifiant.'99', array()))
			 ->getView($this->design, $this->view)->rows);
    }
    
}  
