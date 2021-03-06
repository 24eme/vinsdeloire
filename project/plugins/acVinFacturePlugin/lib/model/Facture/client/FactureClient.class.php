<?php
class FactureClient extends acCouchdbClient {

    const FACTURE_LIGNE_ORIGINE_TYPE_DRM = "DRM";
    const FACTURE_LIGNE_ORIGINE_TYPE_SV12 = "SV12";
    const FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE = "propriete";
    const FACTURE_LIGNE_MOUVEMENT_TYPE_CONTRAT = "contrat";
    const FACTURE_LIGNE_MOUVEMENT_TYPE_NEGOCIANT = "négociant";

    const FACTURE_LIGNE_PRODUIT_TYPE_VINS = "contrat_vins";
    const FACTURE_LIGNE_PRODUIT_TYPE_MOUTS = "contrat_mouts";
    const FACTURE_LIGNE_PRODUIT_TYPE_RAISINS = "contrat_raisins";
    const FACTURE_LIGNE_PRODUIT_TYPE_ECART = "ecart";

    const STATUT_REDRESSEE = 'redressee';
    const STATUT_NONREDRESSABLE = 'non redressable';

    const TYPE_FACTURE_MOUVEMENT_DRM = "MOUVEMENTS_DRM";
    const TYPE_FACTURE_MOUVEMENT_SV12 = "MOUVEMENTS_SV12";
    const TYPE_FACTURE_MOUVEMENT_SV12_NEGO = "MOUVEMENTS_SV12_NEGO";
    const TYPE_FACTURE_MOUVEMENT_DIVERS = "MOUVEMENTS_DIVERS";

    public static $origines = array(self::FACTURE_LIGNE_ORIGINE_TYPE_DRM, self::FACTURE_LIGNE_ORIGINE_TYPE_SV12);

    public static function getInstance() {
        return acCouchdbManager::getClient("Facture");
    }

    public function getId($identifiant, $numeroFacture) {
        return 'FACTURE-'.$identifiant.'-'.$numeroFacture;
    }


    public function getNextNoFacture($idClient,$date)
    {
        $id = '';
    	$facture = self::getAtDate($idClient,$date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        if (count($facture) > 0) {
            $id .= ((double)str_replace('FACTURE-'.$idClient.'-', '', max($facture)) + 1);
        } else {
            $id.= $date.'01';
        }
        return $id;
    }

    public function getAtDate($idClient,$date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('FACTURE-'.$idClient.'-'.$date.'00')->endkey('FACTURE-'.$idClient.'-'.$date.'99')->execute($hydrate);
    }

    public function createDoc($mvts, $societe, $date_facturation = null, $message_communication = null) {
        $facture = new Facture();
        $facture->storeDatesCampagne($date_facturation);
        $facture->constructIds($societe);
        $facture->storeEmetteur();
        $facture->storeDeclarant();
        $facture->storeLignes($mvts, $societe->famille);
        $facture->updateTotalHT();
        $facture->updateAvoir();
        $facture->updateTotaux();
        $facture->storeOrigines();
        if(trim($message_communication)) {
          $facture->addOneMessageCommunication($message_communication);
        }
        $facture->updatePrelevementAutomatique();
        return $facture;
    }

    private $documents_origine = array();
    public function getDocumentOrigine($id) {
        if (!array_key_exists($id, $this->documents_origine)) {
            $this->documents_origine[$id] = acCouchdbManager::getClient()->find($id);
        }
        return $this->documents_origine[$id];
    }

    public function findByIdentifiant($identifiant) {
        return $this->find('FACTURE-' . $identifiant);
    }

    public function findBySocieteAndId($idSociete, $idFacture) {
        return $this->find('FACTURE-'.$idSociete . '-' . $idFacture);
    }

    private function getReduceLevelForFacturation() {
      return MouvementfactureFacturationView::KEYS_VRAC_DEST + 1;
    }

    public function getFacturationForSociete($societe) {
      return MouvementfactureFacturationView::getInstance()->getMouvementsBySocieteWithReduce($societe, 0, 1, $this->getReduceLevelForFacturation());
    }

    public function getMouvementsForMasse($regions) {
        if(!$regions){
            return MouvementfactureFacturationView::getInstance()->getMouvements(0, 1, $this->getReduceLevelForFacturation());
        }
        $mouvementsByRegions = array();
        foreach ($regions as $region) {
            $mouvementsByRegions = array_merge(MouvementfactureFacturationView::getInstance()->getMouvementsFacturablesByRegions(0, 1,$region,$this->getReduceLevelForFacturation()),$mouvementsByRegions);
        }
       return $mouvementsByRegions;
    }

    public function getMouvementsNonFacturesBySoc($mouvements) {
        $generationFactures = array();
        foreach ($mouvements as $mouvement) {
        $societe_id = substr($mouvement->key[MouvementfactureFacturationView::KEYS_ETB_ID], 0, -2);
         if (isset($generationFactures[$societe_id])) {
           $generationFactures[$societe_id][] = $mouvement;
         } else {
           $generationFactures[$societe_id] = array();
           $generationFactures[$societe_id][] = $mouvement;
         }
       }
        return $generationFactures;
    }

    public function filterWithParameters($mouvementsBySoc, $parameters) {
        if (isset($parameters['date_mouvement']) && ($parameters['date_mouvement'])){
          $date_mouvement = Date::getIsoDateFromFrenchDate($parameters['date_mouvement']);
          foreach ($mouvementsBySoc as $identifiant => $mouvements) {
              foreach ($mouvements as $key => $mouvement) {
                      $farDateMvt = $this->getGreatestDate($mouvement->value[MouvementfactureFacturationView::VALUE_DATE]);
                      if(Date::sup($farDateMvt,$date_mouvement)) {
  		                    unset($mouvements[$key]);
                          $mouvementsBySoc[$identifiant] = $mouvements;
                          continue;
                      }

                      if(isset($parameters['type_document']) && !in_array($parameters['type_document'], self::$origines)) {
                          unset($mouvements[$key]);
                          $mouvementsBySoc[$identifiant] = $mouvements;
                          continue;
                      }

                      if(isset($parameters['type_document']) && $parameters['type_document'] != $mouvement->key[MouvementfactureFacturationView::KEYS_ORIGIN]) {
                        unset($mouvements[$key]);
                        $mouvementsBySoc[$identifiant] = $mouvements;
                        continue;
                      }
              }
          }
        }
        foreach ($mouvementsBySoc as $identifiant => $mouvements) {
            $somme = 0;
            foreach ($mouvements as $key => $mouvement) {
                $prix = $mouvement->value[MouvementfactureFacturationView::VALUE_VOLUME] * $mouvement->value[MouvementfactureFacturationView::VALUE_CVO];
                if(!$prix) {
                  unset($mouvementsBySoc[$identifiant][$key]);
                  continue;
                }
                $somme += $prix;
            }
	          $somme = $somme * -1;
            $somme = $this->ttc($somme);

            if(count($mouvementsBySoc[$identifiant]) == 0) {
              $mouvementsBySoc[$identifiant] = null;
            }

            if (isset($parameters['seuil']) && $parameters['seuil']) {
                if (($somme < $parameters['seuil']) && ($somme >= 0)) {
                    $mouvementsBySoc[$identifiant] = null;
                }
          }

      }
      $mouvementsBySoc = $this->cleanMouvementsBySoc($mouvementsBySoc);
      return $mouvementsBySoc;
    }

    private function getGreatestDate($dates){
        if(is_string($dates)) return $dates;
        if(is_array($dates)){
            $dateres = $dates[0];
            foreach ($dates as $date) {
                if(Date::sup($date, $dateres)) $dateres=$date;
            }
            return $dateres;
        }
         throw new sfException("La date du mouvement ou le tableau de date est mal formé ".print_r($dates, true));
    }

    private function cleanMouvementsBySoc($mouvementsBySoc){
      if (count($mouvementsBySoc) == 0)
	return null;
      foreach ($mouvementsBySoc as $identifiant => $mouvement) {
	if (!count($mouvement))
	  unset($mouvementsBySoc[$identifiant]);
      }
      return $mouvementsBySoc;
    }


    public function createFacturesBySoc($generationFactures, $date_facturation, $message_communication = null, $generation = null) {
        if(!$generation){
            $generation = new Generation();
            $generation->type_document = GenerationClient::TYPE_DOCUMENT_FACTURES;
        }
        $generation->date_emission = date('Y-m-d-H:i');
        $generation->documents = array();
        $generation->somme = 0;
        $cpt = 0;

        foreach ($generationFactures as $societeID => $mouvementsSoc) {
            $societe = SocieteClient::getInstance()->find($societeID);
            $f = $this->createDoc($mouvementsSoc, $societe, $date_facturation, $message_communication);
            if($generation->type_document == GenerationClient::TYPE_DOCUMENT_FACTURES_DRM){
              $f->add('facture_electronique',true);
            }
            $f->save();

            $generation->somme += $f->total_ttc;
            $generation->add('documents')->add($cpt, $f->_id);
            $cpt++;
        }

        return $generation;
    }

    private function ttc($p) {
        return $p + $p * 0.196;
    }

    public function getTypes() {
        return array(FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_VINS,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_RAISINS,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_ECART,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_MOUTS);
    }

    public function getProduitsFromTypeLignes($lignes) {
        $produits = array();
        foreach ($lignes as $ligne) {
            if (array_key_exists($ligne->produit_hash, $produits)) {
                $produits[$ligne->produit_hash][] = $ligne;
            } else {
                $produits[$ligne->produit_hash] = array();
                $produits[$ligne->produit_hash][] = $ligne;
            }
        }
        return $produits;
    }

    public function isRedressee($factureview){
      return ($factureview->value[FactureEtablissementView::VALUE_STATUT] == self::STATUT_REDRESSEE);
    }

    public function isRedressable($factureview){
      return !$this->isRedressee($factureview) && $factureview->value[FactureEtablissementView::VALUE_STATUT] != self::STATUT_NONREDRESSABLE;
    }

    public function getTypeLignePdfLibelle($typeLibelle) {
      if ($typeLibelle == self::FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE)
	return 'Sorties de propriété';
      switch ($typeLibelle) {
      case self::FACTURE_LIGNE_PRODUIT_TYPE_MOUTS:
	return 'Sorties de contrats moûts';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_RAISINS:
	return 'Sorties de contrats raisins';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_VINS:
	return 'Sorties de contrats vins';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_ECART:
	return 'Sorties raisins et moûts';
      }
      return '';
    }

    public function defactureCreateAvoirAndSaveThem(Facture $f) {
      if (!$f->isRedressable()) {
	       return ;
      }
      $avoir = clone $f;
      $soc = SocieteClient::getInstance()->find($avoir->identifiant);
      $avoir->constructIds($soc, $f->region);
      $f->add('avoir',$avoir->_id);
      $f->save();
      foreach($avoir->lignes as $type => $lignes) {
	foreach($lignes as $id => $ligne) {
	  $ligne->volume *= -1;
	  $ligne->montant_ht *= -1;
          $ligne->echeance_code = null;
	}
      }
      $avoir->total_ttc *= -1;
      $avoir->total_ht *= -1;
      $avoir->remove('echeances');
      $avoir->add('echeances');
      $avoir->statut = self::STATUT_NONREDRESSABLE;
      $avoir->storeDatesCampagne(date('Y-m-d'));
      $avoir->numero_archive = null;
      $avoir->numero_interloire = null;
      $avoir->versement_comptable = 0;
      $avoir->add('taux_tva', round($f->getTauxTva(),2));
      $avoir->save();
      $f->defacturer();
      $f->save();
      return $avoir;
    }

    public function getDateCreation($id) {
        $d = substr($id, -10,8);
        $matches = array();
        if(preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', $d, $matches)){
        return $matches[3].'/'.$matches[2].'/'.$matches[1];
        }
        return '';
    }

}
