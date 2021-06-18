<?php

/**
 * Model for Facture
 *
 */
class Facture extends BaseFacture implements InterfaceArchivageDocument {

    private $documents_origine = array();
    protected $declarant_document = null;
    protected $archivage_document = null;

    const MESSAGE_DEFAULT = "ICI le message par défaut : Vous pouvez retrouver nos infos jours après jours sur http://www.vinsvaldeloire.fr";

    public function __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }

    protected function initDocuments() {
        $this->declarant_document = new DeclarantDocument($this);
        $this->archivage_document = new ArchivageDocument($this);
    }

    public function getCampagne() {

        return $this->_get('campagne');
    }

    public function storeEmetteur() {
        $configs = sfConfig::get('app_facture_emetteur');
        $emetteur = new stdClass();


        if (!array_key_exists($this->region, $configs))
            throw new sfException(sprintf('Config %s not found in app.yml', $this->region));
        $this->emetteur = $configs[$this->region];
    }

    public function getCoordonneesBancaire() {
        $coordonneesBancaires = new stdClass();
        $coordonneesBancaires->banque = 'Crédit Agricole Touraine Poitou';
        $coordonneesBancaires->bic = ' AGRIFRPP894';
        $coordonneesBancaires->iban = ' FR76~1940~6370~1579~1722~5300~105';
        $coordonneesBancaires->siret = ' 429 164 072 00143';
        $coordonneesBancaires->codeApe = ' APE 9499 Z';
        $coordonneesBancaires->tvaIntracom = ' FR 73 429164072';
        return $coordonneesBancaires;
    }

    public function storeDatesCampagne($date_facturation) {
        $this->date_emission = date('Y-m-d');
        $this->date_facturation = $date_facturation;
        if (!$this->date_facturation)
            $this->date_facturation = date('Y-m-d');
        $this->campagne = ConfigurationClient::getInstance()->getCampagneFacturation()->getCampagneByDate($this->date_facturation);
    }

    public function constructIds($soc) {
        if (!$soc)
            throw new sfException('Pas de societe attribuée');
        $this->region = $soc->getRegionViticole();
        $this->identifiant = $soc->identifiant;
        $this->numero_facture = FactureClient::getInstance()->getNextNoFacture($this->identifiant, date('Ymd'));
        $this->_id = FactureClient::getInstance()->getId($this->identifiant, $this->numero_facture);
    }

    public function getNumeroInterloire() {
        if ($this->_get('numero_interloire')) {

            return $this->_get('numero_interloire');
        }

        return preg_replace('/^\d{2}(\d{2}).*/', '$1', $this->date_facturation) . '/' . $this->getPrefixForRegion() . '-' . $this->numero_archive;
    }

    public function getNumeroReference() {
        return substr($this->numero_facture, 6, 2) . ' ' . substr($this->numero_facture, 0, 6);
    }

    public function getTaxe() {
        return $this->total_ttc - $this->total_ht;
    }

    public function facturerMouvements() {
        foreach ($this->getLignes() as $l) {
            $l->facturerMouvements();
        }
    }

    public function defacturer() {
        if (!$this->isRedressable())
            return;
        foreach ($this->getLignes() as $ligne) {
            $ligne->defacturerMouvements();
        }
        $this->statut = FactureClient::STATUT_REDRESSEE;
    }

    public function isRedressee() {
        return ($this->statut == FactureClient::STATUT_REDRESSEE);
    }

    public function isRedressable() {
        return ($this->statut != FactureClient::STATUT_REDRESSEE && $this->statut != FactureClient::STATUT_NONREDRESSABLE);
    }

    public function getEcheances() {
        $e = $this->_get('echeances')->toArray();
        usort($e, 'Facture::triEcheanceDate');
        return $e;
    }

    public function getLignesArray() {
        $l = $this->_get('lignes')->toArray();
        usort($l, 'Facture::triOrigineDate');
        return $l;
    }

    public function getNbLignesAndDetails() {
      $nb = 0;
      foreach($this->lignes as $k => $l) {
        $nb++;
        $nb += count($l);
      }
      return $nb;
    }

    static function triOrigineDate($ligne_0, $ligne_1) {
        return self::triDate("origine_date", $ligne_0, $ligne_1);
    }

    static function triEcheanceDate($ligne_0, $ligne_1) {
        return self::triDate("echeance_date", $ligne_0, $ligne_1);
    }

    static function triDate($champ, $ligne_0, $ligne_1) {
        if ($ligne_0->{$champ} == $ligne_1->{$champ}) {

            return 0;
        }
        return ($ligne_0->{$champ} > $ligne_1->{$champ}) ? -1 : +1;
    }

    public function storeLignes($mvts, $famille) {
        foreach ($mvts as $lignesByType) {
            $this->storeLigne($lignesByType, $famille);
        }
    }

    public function storeLigne($ligneByType, $famille) {
        $ligne = $this->lignes->add($ligneByType->key[MouvementfactureFacturationView::KEYS_MATIERE])->add();
        $ligne->cotisation_taux = $ligneByType->value[MouvementfactureFacturationView::VALUE_CVO];
        $ligne->volume = $ligneByType->value[MouvementfactureFacturationView::VALUE_VOLUME];
        $ligne->origine_type = $ligneByType->key[MouvementfactureFacturationView::KEYS_ORIGIN];
        $ligne->origine_identifiant = $ligneByType->value[MouvementfactureFacturationView::VALUE_NUMERO];
        $ligne->contrat_identifiant = $ligneByType->key[MouvementfactureFacturationView::KEYS_CONTRAT_ID];
        $ligne->origine_date = $ligneByType->key[MouvementfactureFacturationView::KEYS_PERIODE];
        $ligne->produit_type = $ligneByType->key[MouvementfactureFacturationView::KEYS_MATIERE];
        $ligne->produit_libelle = $ligneByType->value[MouvementfactureFacturationView::VALUE_PRODUIT_LIBELLE];
        $ligne->produit_hash = $ligneByType->key[MouvementfactureFacturationView::KEYS_PRODUIT_ID];
        $ligne->montant_ht = round($ligne->cotisation_taux * $ligne->volume * -1, 2);
        $ligne->origine_mouvements = $this->createLigneOriginesMouvements($ligne, $ligneByType->value[MouvementfactureFacturationView::VALUE_ID_ORIGINE]);
        $transacteur = $ligneByType->value[MouvementfactureFacturationView::VALUE_VRAC_DEST];
        $ligne->origine_libelle = $this->createOrigineLibelle($ligne, $transacteur, $famille, $ligneByType);

        $this->verifLigneAndVolumeOrigines($ligne);
    }

    protected function verifLigneAndVolumeOrigines($ligne) {
        $volume = 0;
        foreach ($ligne->origine_mouvements as $doc_id => $keys) {
            $doc = acCouchdbManager::getClient()->find($doc_id, acCouchdbClient::HYDRATE_JSON);
            foreach ($keys as $key) {
                foreach ($doc->mouvements as $identifiant => $mouvements) {
                    if (!preg_match("/^" . $this->identifiant . "/", $identifiant)) {
                        continue;
                    }

                    $mouvement = $mouvements->$key;
                    $volume += $mouvement->volume;
                }
            }
        }
        if (abs($ligne->volume - $volume) > 0.01) {

            throw new sfException(sprintf("%s : Le volume de la ligne %s de %s hl ne correspond pas à la somme des volumes des mouvements %s hl", $this->_id, $ligne->getKey(), round($ligne->volume, 2), round($volume, 2)));
        }
    }

    private function createLigneOriginesMouvements($ligne, $originesTable) {
        $origines = array();
        foreach ($originesTable as $origineFormatted) {
            $origineKeyValue = explode(':', $origineFormatted);
            if (count($origineKeyValue) != 2)
                throw new Exception('Le mouvement est mal formé : %s', print_r($origineKeyValue));
            $key = $origineKeyValue[0];
            $value = $origineKeyValue[1];
            if (!array_key_exists($key, $origines)) {
                $origines[$key] = array();
            }
            $origines[$key][] = $value;
        }

        return $origines;
    }

    private function createOrigineLibelle($ligne, $transacteur, $famille, $view) {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Date'));
        if ($ligne->origine_type == FactureClient::FACTURE_LIGNE_ORIGINE_TYPE_SV12) {
            if ($ligne->produit_type == FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_ECART) {
                $origine_libelle = " (" . $transacteur . ") " . SV12Client::getInstance()->getLibelleFromId($ligne->origine_identifiant);
                return $origine_libelle;
            }
            $origine_libelle = 'n° ' . $view->value[MouvementfactureFacturationView::VALUE_DETAIL_LIBELLE];
            $origine_libelle .= ' (' . $transacteur . ') ';
            if ($famille == EtablissementFamilles::FAMILLE_NEGOCIANT)
                $origine_libelle .= SV12Client::getInstance()->getLibelleFromId($ligne->origine_identifiant);
            return $origine_libelle;
        }

        if ($ligne->origine_type == FactureClient::FACTURE_LIGNE_ORIGINE_TYPE_DRM) {
            if ($ligne->produit_type == FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_VINS) {
                if ($famille == EtablissementFamilles::FAMILLE_PRODUCTEUR) {
                    $origine_libelle = 'n° ' . $view->value[MouvementfactureFacturationView::VALUE_DETAIL_LIBELLE];
                } else {
                    $origine_libelle = 'n° ' . $view->value[MouvementfactureFacturationView::VALUE_DETAIL_LIBELLE] . ' enlèv. au ' . format_date($view->value[MouvementfactureFacturationView::VALUE_DATE], 'dd/MM/yyyy') . ' ';
                }
                $origine_libelle .= ' (' . $transacteur . ') ';
                if ($famille == EtablissementFamilles::FAMILLE_PRODUCTEUR)
                    $origine_libelle .= DRMClient::getInstance()->getLibelleFromId($ligne->origine_identifiant);
                return $origine_libelle;
            }
            return DRMClient::getInstance()->getLibelleFromId($ligne->origine_identifiant);
        }
    }

//    private function troncate($origine_libelle, $produit_libelle) {
//        if ((strlen($produit_libelle) * 1.5 + strlen($origine_libelle)) > 124) {
//            $max = 124 - (strlen($produit_libelle) * 1.5) - 4;
//            $origine_libelle = substr($origine_libelle, 0, $max) . '...';
//            if (strstr($origine_libelle, "(") !== FALSE)
//                $origine_libelle.=')';
//        }
//        return $origine_libelle;
//    }

    public function storePapillons() {
        foreach ($this->lignes as $typeLignes) {
            foreach ($typeLignes as $ligne) {
                switch ($ligne->produit_type) {
                    case FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_MOUTS:
                    case FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_RAISINS:
                        if (strstr($ligne->produit_hash, 'mentions/LIE/')) {
                            $this->createOrUpdateEcheanceD($ligne);
                            break;
                        }
                        if ($this->isContratPluriannuel($ligne))
                            $this->createOrUpdateEcheanceC($ligne);
                        else
                            $this->createOrUpdateEcheanceB($ligne);
                        break;
                    case FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_ECART:
                        $this->createOrUpdateEcheanceB($ligne);
                        break;
                    case FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_VINS:
                    default :
                        $this->createOrUpdateEcheanceA($ligne);
                        break;
                }
            }
        }
    }

    public function updateAvoir() {
        if ($this->total_ht > 0) {
            $this->storePapillons();
        } else {
            $this->removeCodesEcheances();
        }
    }

    private function removeCodesEcheances() {
        foreach ($this->getLignes() as $typeLigne) {
            foreach ($typeLigne as $ligne) {
                $ligne->echeance_code = null;
            }
        }
    }

    private function isContratPluriannuel($l) {
        $contrat = VracClient::getInstance()->findByNumContrat($l->contrat_identifiant, acCouchdbClient::HYDRATE_JSON);
        if (!$contrat->type_contrat)
            throw new sfException("Le contrat de numéro $l->contrat_identifiant n'est pas valide.");
        return ($contrat->type_contrat == VracClient::TYPE_CONTRAT_PLURIANNUEL);
    }


    public function createOrUpdateEcheanceC($ligne) {
        $ligne->echeance_code = 'C';
        $date = str_replace('-', '', $this->date_facturation);

        $d1 = date('Y') . '0331'; // 31/03/N
        $d2 = date('Y') . '0630'; // 30/06/N
        $d3 = date('Y') . '0930'; // 30/09/N
        //if( date < 31/03/N) { 33% 31/03/N 33% 30/06/N et 33% 30/09/N }
        if ($date < $d1) {
            $this->updateEcheance('C', date('Y') . '-03-31', $ligne->montant_ht * (1 / 3));
            $this->updateEcheance('C', date('Y') . '-06-30', $ligne->montant_ht * (1 / 3));
            $this->updateEcheance('C', date('Y') . '-09-30', $ligne->montant_ht * (1 / 3));
            return;
        }

        //if(01/04/N < date < 31/05/N)   { 50% au 30/06/N et 50% 30/09/N}
        if ($date < $d2) {
            $this->updateEcheance('C', date('Y') . '-06-30', $ligne->montant_ht * 0.5);
            $this->updateEcheance('C', date('Y') . '-09-30', $ligne->montant_ht * 0.5);
            return;
        }

        //if(30/06/N < date < 30/09/N) { 100% 30/09/N }
        if ($date < $d3) {
            $this->updateEcheance('C', date('Y') . '-09-30', $ligne->montant_ht);
            return;
        }

        //Dépassement de délais -> 100% comptant
        $this->createOrUpdateEcheanceE($ligne);
    }

    public function createOrUpdateEcheanceB($ligne) {
        $ligne->echeance_code = 'B';
        $date = str_replace('-', '', $this->date_facturation);

        $d1 = date('Y') . '0331'; // 31/03/N
        $d2 = date('Y') . '0630'; // 30/06/N
        //if( date < 31/03/N) { 50% 31/03/N 50% 30/06/N}
        if ($date < $d1) {
            $this->updateEcheance('B', date('Y') . '-03-31', $ligne->montant_ht * 0.5);
            $this->updateEcheance('B', date('Y') . '-06-30', $ligne->montant_ht * 0.5);
            return;
        }
        //if(01/04/N <= date < 30/06/N)   { 100% au 30/06 }
        if ($date < $d2) {
            $this->updateEcheance('B', date('Y') . '-06-30', $ligne->montant_ht);
            return;
        }

        //Dépassement de délais -> 100% comptant
        $this->createOrUpdateEcheanceE($ligne);
    }

    public function createOrUpdateEcheanceA($ligne) {
        $ligne->echeance_code = 'A';
        $this->updateEcheance('A', Date::getIsoDateFinDeMoisISO($this->date_facturation, 2), $ligne->montant_ht);
    }

    public function createOrUpdateEcheanceD($ligne) {
        $ligne->echeance_code = 'D';
        $date = date('Y') . '0930';
        $dateEcheance = date('Y') . '-09-30';
        if (str_replace('-', '', $this->date_facturation) < $date) {
            $this->updateEcheance('D', $dateEcheance, $ligne->montant_ht);
            return;
        }
        //Dépassement de délais -> 100% comptant
        $this->createOrUpdateEcheanceE($ligne);
    }

    public function createOrUpdateEcheanceE($ligne) {
        $ligne->echeance_code = 'E';
        $this->updateEcheance('E', $this->date_facturation, $ligne->montant_ht);
    }

    public function updateEcheance($echeance_code, $date, $montant_ht) {
        //Vérifie qu'il n'y a pas d'échéance à la même date avant de ajouter une nouvelle
        foreach ($this->echeances as $echeance) {
            if ($echeance->echeance_date == $date) {
                $echeance->montant_ttc += $this->ttc($montant_ht);
                if (strstr($echeance->echeance_code, $echeance_code) === FALSE)
                    $echeance->echeance_code.=' + ' . $echeance_code;
                return;
            }
        }
        //Ici on est sur qu'il n'y a pas d'échéance à cette date, alors on l'ajoute
        $echeance = new stdClass();
        $echeance->echeance_code = $echeance_code;
        $echeance->montant_ttc = $this->ttc($montant_ht);
        $echeance->echeance_date = $date;
        $this->add("echeances")->add(count($this->echeances), $echeance);
    }

    public function storeOrigines() {
        foreach ($this->getLignes() as $lignesType) {
            foreach ($lignesType as $ligne) {
                foreach ($ligne->origine_mouvements as $idorigine => $null) {
                    if (!array_key_exists($idorigine, $this->origines))
                        $this->origines->add($idorigine, $idorigine);
                }
            }
        }
    }

    public function updateTotaux() {
        $this->updateTotalHT();
        $this->updateTotalTTC();
        $this->updateTotalTaxe();
    }

    public function updateTotalHT() {
        $this->total_ht = 0;
        foreach ($this->lignes as $typeLignes) {
            foreach ($typeLignes as $ligne) {
                $this->total_ht += $ligne->montant_ht;
            }
        }

        if (round($this->total_ht, 4) == 0) {
            $this->total_ht = 0;
        }
    }

    public function updateTotalTTC() {
        $this->total_ttc = 0;
        if (!$this->echeances)
            $this->total_ttc = $this->ttc($this->total_ht);
        foreach ($this->echeances as $echeance) {
            $this->total_ttc += $echeance->montant_ttc;
        }
    }

    public function updateTotalTaxe() {
        $this->total_taxe = $this->total_ttc - $this->total_ht;
        $this->add('taux_tva', $this->getTauxTva());
    }

    public function getNbLignesMouvements() {
        $nbLigne = 0;
        foreach ($this->lignes as $lignesType) {
            $nbLigne += count($lignesType) + 1;
        }
        return $nbLigne;
    }

    protected function ttc($p) {
        $taux_tva = $this->getTauxTva() / 100;
        return round($p + $p * $taux_tva, 2);
    }

    public function getTauxTva() {
        if ($this->exist('taux_tva') && $this->_get('taux_tva')) {
            return round($this->_get('taux_tva'), 2);
        }
        $config_tva = sfConfig::get('app_tva_taux');
        $date_facturation = str_replace('-', '', $this->date_facturation);
        $taux_f = 0.0;
        foreach ($config_tva as $date => $taux) {
            if ($date_facturation >= $date) {
                $taux_f = round($taux, 2);
            }
        }
        return $taux_f;
    }

    public function save() {
        parent::save();
        $this->saveDocumentsOrigine();
    }

    public function saveDocumentsOrigine() {
        foreach ($this->origines as $docid) {
            $doc = FactureClient::getInstance()->getDocumentOrigine($docid);
            if ($doc) {
                $doc->save();
            }
        }
    }

    protected function preSave() {
        if ($this->isNew() && $this->statut != FactureClient::STATUT_REDRESSEE) {
            $this->facturerMouvements();
            $this->storeOrigines();
        }
        if (!$this->versement_comptable) {
            $this->versement_comptable = 0;
        }

        $this->archivage_document->preSave();
        $this->numero_interloire = $this->getNumeroInterloire();
    }

    public function storeDeclarant() {
        $declarant = $this->declarant;
        $declarant->nom = $this->societe->raison_sociale;
        $declarant->num_tva_intracomm = $this->societe->no_tva_intracommunautaire;
        $declarant->adresse = $this->societe->getSiegeAdresses();
        $declarant->commune = $this->societe->siege->commune;
        $declarant->code_postal = $this->societe->siege->code_postal;
        $declarant->raison_sociale = $this->societe->raison_sociale;
        $this->code_comptable_client = $this->societe->code_comptable_client;
    }

    public function getCodeComptableClient() {
        $code = $this->_get('code_comptable_client');
        if (!$code) {
            $code = $this->societe->code_comptable_client;
            $this->_set('code_comptable_client', $code);
        }
        return $code;
    }

    public function getSociete() {
        return SocieteClient::getInstance()->find($this->identifiant);
    }

    public function getPrefixForRegion() {
        return EtablissementClient::getPrefixForRegion($this->region);
    }

    public function hasAvoir() {
        return ($this->exist('avoir') && !is_null($this->get('avoir')));
    }

    public function isAvoir() {
        return (($this->exist('statut')) &&
                ($this->statut == FactureClient::STATUT_NONREDRESSABLE) &&
                ($this->exist("total_ht")) &&
                ($this->total_ht < 0.0));
    }

    public function getDateRemboursement(){
      if(!$this->isAvoir() || !$this->isPrelevementAutomatique()){
        return false;
      }
      $facturesSource = FactureEtablissementView::getInstance()->findBySociete($this->getSociete());
      $dateRemboursement = date("Y-m-d");
      foreach ($facturesSource as $factureRow) {
        if($factureRow->value[FactureEtablissementView::VALUE_STATUT] == FactureClient::STATUT_REDRESSEE){
          $f = FactureClient::getInstance()->find($factureRow->id);
          if($f->exist("avoir") && $f->avoir == $this->_id){
              foreach ($f->echeances as $e) {
                if($e->echeance_date > $dateRemboursement){
                  $dateRemboursement = $e->echeance_date;
                }
              }
          }
        }
      }
      return $dateRemboursement;
    }

    /*     * * ARCHIVAGE ** */

    public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function setVerseEnCompta() {
        return $this->_set('versement_comptable', 1);
    }

    public function setDeVerseEnCompta() {
        return $this->_set('versement_comptable', 0);
    }

    public function setDateFacturation($d) {
	    if (!preg_match('/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/', $d)) {
            $m = array();
            if (!preg_match('/^[0-9][0-9]\/[0-9][0-9]\/[0-9][0-9][0-9][0-9]$/', $d,$m)) {
                var_dump($m); exit;
    		    throw new sfException("Mauvais format de date");
    	    }
		    throw new sfException("Mauvais format de date");
	    }
	    return $this->_set('date_facturation', $d);
    }

    public function isArchivageCanBeSet() {

        return true;
    }

    public function addOneMessageCommunication($message_communication = null) {
        $this->add('message_communication', $message_communication);
    }

    public function hasMessageCommunication() {
        return $this->exist('message_communication');
    }

    public function getMessageCommunicationWithDefault() {
        if ($this->exist('message_communication')) {
            return $this->_get('message_communication');
        }
        return self::MESSAGE_DEFAULT;
    }

    public function updatePrelevementAutomatique(){
      if($this->getSociete()->hasInfosSepa()
      && $this->getSociete()->getMasterCompte()->hasDroit(ROLES::TELEDECLARATION_PRELEVEMENT)){
        if($this->getSociete()->getSepaDateEffectif() <= $this->getDateFacturation()){
          $this->add('prelevement_automatique',true);
        }
      }
    }

    public function isPrelevementAutomatique(){
      return $this->exist('prelevement_automatique') && $this->get('prelevement_automatique');
     }

    /*     * * FIN ARCHIVAGE ** */
}
