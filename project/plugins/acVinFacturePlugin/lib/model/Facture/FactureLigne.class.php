<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class FactureLigne
 * @author mathurin
 */
class FactureLigne extends BaseFactureLigne {
    
   public function getMouvements() {
     $mouvements = array();        
     foreach ($this->origine_mouvements as $idDoc => $mouvsKeys) {
       foreach ($mouvsKeys as $mouvKey) {
	 $mouvements[] = Factureclient::getInstance()->getDocumentOrigine($idDoc)->findMouvement($mouvKey, $this->getDocument()->identifiant);
       }
     }
     return $mouvements;
   }
   
    public function facturerMouvements() {       
        foreach ($this->getMouvements() as $mouv) {
            $mouv->facturer();
        }
    }
   
    public function setProduitHash($ph) {
      $ret = $this->_set('produit_hash', $ph);
      //Remove identifiant_analytique from cache and set the new one
      $this->_set('produit_identifiant_analytique', null);
      $this->getProduitIdentifiantAnalytique();
      return $ret;
    }
    

    public function getProduitIdentifiantAnalytique() {
      $id = $this->_get('produit_identifiant_analytique');
      if ($id) {
	return $id;
      }
      $code = $this->getConfProduit()->getCodeComptable();
      $this->_set('produit_identifiant_analytique', $code);
      return $code;
    }

    public function getConfProduit() {
      return ConfigurationClient::getCurrent()->get($this->produit_hash);
    }

    public function defacturerMouvements() {
        foreach ($this->getMouvements() as $mouv) {
               $mouv->defacturer();
        }
    }
    
}
