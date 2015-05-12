<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracStatutAndTypeView
 * @author mathurin
 */
class VracSoussigneIdentifiantView extends acCouchdbView {

    const VALUE_STATUT = 1;
    const VALUE_PRODUIT_HASH = 12;

    public static function getInstance() {
        return acCouchdbManager::getView('vrac', 'soussigneidentifiant', 'Vrac');
    }

    public function getProduitHashesFromCampagneAndAcheteur($campagne, $etablissement) {
      $produits = array();
      $rows = $this->client->startkey(array("TYPE", $etablissement->identifiant, $campagne))
                        	 ->endkey(array("TYPE", $etablissement->identifiant, $campagne, array()))
                           ->reduce(false)
                        	 ->getView($this->design, $this->view)->rows;
      foreach($rows as $row) {
        if(!in_array($row->value[self::VALUE_STATUT], VracClient::$statuts_valide)) {
          continue;
        }
	      $produits[$row->value[self::VALUE_PRODUIT_HASH]] = 1;
      }
      return array_keys($produits);
    }
}

