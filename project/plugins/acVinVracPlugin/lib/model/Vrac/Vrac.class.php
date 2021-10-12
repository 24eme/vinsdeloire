<?php

/**
 * Model for Vrac
 *
 */
class Vrac extends BaseVrac {

    protected $archivage_document = null;

    public function __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }

    protected function initDocuments() {
        $this->archivage_document = new ArchivageDocument($this);
    }

    public function constructId() {
        $this->set('_id', 'VRAC-' . $this->numero_contrat);

        if (!$this->date_signature) {
            $this->date_signature = date('d/m/Y');
        }

        if (!$this->date_campagne) {
            $this->date_campagne = date('d/m/Y');
        }
    }

    public function getCampagne() {

        return $this->_get('campagne');
    }

    public function setNumeroContrat($value) {
        if ($this->isTeledeclare()) {
            $value = preg_replace('/.(....)$/', '1$1', $value);
        }
        $this->_set('numero_contrat', $value);
    }

    public function setProduit($value) {
        if ($value != $this->_get('produit')) {
            $this->_set('produit', $value);
            $this->produit_libelle = $this->getConfigProduit()->getLibelleFormat();
        }
    }

    public function setBouteillesContenanceLibelle($c) {
        $this->_set('bouteilles_contenance_libelle', $c);
        if ($c) {
            $this->setBouteillesContenanceVolume(VracClient::getInstance()->getContenance($c));
        }
    }

    public function update($params = array()) {

        $this->prix_initial_total = null;
        switch ($this->type_transaction) {
            case VracClient::TYPE_TRANSACTION_RAISINS : {
                    $this->prix_initial_total = round($this->raisin_quantite * $this->prix_initial_unitaire, 2);
                    $this->bouteilles_contenance_libelle = null;
                    $this->bouteilles_contenance_volume = null;
                    $this->volume_propose = round($this->raisin_quantite / $this->getDensite() / 100.0, 2);
                    break;
                }
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE : {
                    $this->prix_initial_total = round($this->bouteilles_quantite * $this->prix_initial_unitaire, 2);
                    $this->volume_propose = round($this->bouteilles_quantite * $this->bouteilles_contenance_volume, 2);
                    break;
                }

            case VracClient::TYPE_TRANSACTION_MOUTS :
            case VracClient::TYPE_TRANSACTION_VIN_VRAC : {
                    $this->prix_initial_total = round($this->jus_quantite * $this->prix_initial_unitaire, 2);
                    $this->bouteilles_contenance_libelle = '';
                    $this->bouteilles_contenance_volume = null;
                    $this->volume_propose = $this->jus_quantite;
                    break;
                }
        }

        if ($this->volume_propose) {
            $this->prix_initial_unitaire_hl = round($this->prix_initial_total / $this->volume_propose * 1.0, 2);
        }

        if ($this->isVise() && !$this->hasPrixVariable()) {
            $this->setPrixUnitaire($this->prix_initial_unitaire);
        }

        if ($this->isTeledeclare()) {
            $this->cvo_repartition = $this->calculCvoRepartition();
            $this->cvo_nature = VracClient::CVO_NATURE_MARCHE_DEFINITIF;
        }
        $this->updateCampagne();
    }

    public function updateCampagne() {
        $array_date = explode('-', $this->getDateSignature('Y-m-d'));
        if (($this->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS ||
            $this->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS)
            && $array_date[1] >= '06'
            && $array_date[1] < '08'
        ) {
            $this->setDateCampagne($array_date[0].'-08-01');
        }
    }

    public function createVisa() {
        $this->valide->statut = VracClient::STATUS_CONTRAT_VISE;
        $this->date_signature = date('Y-m-d H:i:s');
        $this->update();
    }

    public function setInformations() {
        $this->setAcheteurInformations();
        $this->setVendeurInformations();
        if ($this->mandataire_identifiant != null && $this->mandataire_exist) {
            $this->setMandataireInformations();
        }
    }

    public function setVendeurIdentifiant($s) {
        return $this->_set('vendeur_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }

    public function setAcheteurIdentifiant($s) {
        return $this->_set('acheteur_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }

    public function setMandataireIdentifiant($s) {
        return $this->_set('mandataire_identifiant', str_replace('ETABLISSEMENT-', '', $s));
    }

    public function setAcheteurInformations() {
        if ($this->exist('acheteur_identifiant') && $this->acheteur_identifiant) {
            $this->setEtablissementInformations('acheteur', $this->getAcheteurObject());
        }
    }

    private function setMandataireInformations() {
        $etablissement = $this->getMandataireObject();
        $this->mandataire->nom = $etablissement->nom;
        $this->mandataire->raison_sociale = $etablissement->raison_sociale;
        $this->mandataire->adresse = $etablissement->siege->adresse;
        $this->mandataire->commune = $etablissement->siege->commune;
        $this->mandataire->code_postal = $etablissement->siege->code_postal;
        $this->mandataire->carte_pro = $etablissement->carte_pro;
    }

    public function setVendeurInformations() {
        if ($this->exist('vendeur_identifiant') && $this->vendeur_identifiant) {
            $this->setEtablissementInformations('vendeur', $this->getVendeurObject());
        }
    }

    public function initCreateur($etbId) {

        $etbId = str_replace('ETABLISSEMENT-', '', $etbId);
        $etb = EtablissementClient::getInstance()->findByIdentifiant($etbId);
        if (!$etb) {
            throw new sfException("L'etablissement d'id $etbId n'existe pas en base");
        }
        if (!$etb->isCourtier() && !$etb->isNegociant() && !$etb->isNegociantPur()) {
            throw new sfException("La création d'un contrat ne peut pas se faire l'etablissement $etbId n'est ni courtier ni négociant");
        }
        if ($etb->isCourtier()) {
            $this->mandataire_exist = true;
            $this->setMandataireIdentifiant($etbId);
            $this->setMandataireInformations();
        }

        if ($etb->isNegociant() || $etb->isNegociantPur()) {
            $this->setAcheteurIdentifiant($etbId);
            $this->setAcheteurInformations();
        }
        $this->valide->statut = VracClient::STATUS_CONTRAT_BROUILLON;
        $this->setDateCampagne(date('Y-m-d'));
        $this->add('createur_identifiant', $etbId);
        $this->add('teledeclare', true);
    }

    protected function setEtablissementInformations($type, $etablissement) {
        $this->get($type)->nom = $etablissement->nom;
        $this->get($type)->raison_sociale = $etablissement->raison_sociale;
        $this->get($type)->cvi = $etablissement->cvi;
        $this->get($type)->no_accises = $etablissement->no_accises;
        $this->get($type)->no_tva_intracomm = $etablissement->getNoTvaIntraCommunautaire();
        $this->get($type)->adresse = $etablissement->siege->adresse;
        $this->get($type)->commune = $etablissement->siege->commune;
        $this->get($type)->code_postal = $etablissement->siege->code_postal;
        $this->get($type)->region = $etablissement->region;
    }

    public function setDate($attribut, $d) {
        if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $d, $m)) {
            $d = $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        return $this->_set($attribut, $d);
    }

    public function getDate($attribut, $format) {
        $d = $this->_get($attribut);
        if (!$format)
            return $d;
        $date = new DateTime($d);
        return $date->format($format);
    }

    public function setDateSignature($d) {
        $ret = $this->setDate('date_signature', $d);
        $this->updateCampagne();
        return $ret;
    }

    public function getDateSignature($format = 'd/m/Y') {
        return $this->getDate('date_signature', $format);
    }

    public function setDateCampagne($d) {
        $this->setDate('date_campagne', $d);
        if($this->numero_archive && $this->campagne) {

            return;
        }
        $this->campagne = VracClient::getInstance()->buildCampagne($this->getDateCampagne('Y-m-d'));
    }

    public function getDateConfig() {

        return $this->getDateCampagne('Y-m-d');
    }

    public function getPrixUnitaire() {
        if (is_null($this->_get('prix_unitaire'))) {
            return $this->prix_initial_unitaire;
        }

        return $this->_get('prix_unitaire');
    }

    public function getPrixTotalOuInitial() {
        if (is_null($this->_get('prix_total'))) {
            return $this->prix_initial_total;
        }

        return $this->_get('prix_total');
    }

    public function getPrixUnitaireHlOuInitial() {
        if (is_null($this->_get('prix_unitaire_hl'))) {
            return $this->prix_initial_unitaire_hl;
        }

        return $this->_get('prix_unitaire_hl');
    }

    public function setPrixVariable($value) {
        if ($this->_get('prix_variable') != $value) {
            $this->setPrixUnitaire(null);
        }

        $this->_set('prix_variable', $value);
    }

    public function setPrixUnitaire($p) {
        $this->_set('prix_unitaire', $p);

        if (is_null($this->_get('prix_unitaire'))) {
            $this->prix_total = null;
            $this->prix_unitaire_hl = null;

            return;
        }

        switch ($this->type_transaction) {
            case VracClient::TYPE_TRANSACTION_RAISINS : {
                    $this->prix_total = round($this->raisin_quantite * $this->prix_unitaire, 2);
                    break;
                }
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE : {
                    $this->prix_total = round($this->bouteilles_quantite * $this->prix_unitaire, 2);
                    break;
                }

            case VracClient::TYPE_TRANSACTION_MOUTS :
            case VracClient::TYPE_TRANSACTION_VIN_VRAC :
                $this->prix_total = round($this->jus_quantite * $this->prix_unitaire, 2);
                break;
        }

        if ($this->prix_unitaire) {
            $this->prix_unitaire_hl = round($this->prix_total / $this->volume_propose * 1.0, 2);
        }
    }

    public function setCvoRepartition($repartition) {
        if (!is_null($this->volume_enleve) && $this->volume_enleve > 0)
            return;

        $this->_set('cvo_repartition', $repartition);
    }

    public function calculCvoRepartition() {
        if (!$this->getAcheteurObject()->isInterLoire($this->produit)) {

            return VracClient::CVO_REPARTITION_100_VITI;
        }
        if (in_array($this->type_transaction, array(VracClient::TYPE_TRANSACTION_RAISINS, VracClient::TYPE_TRANSACTION_MOUTS))) {

            return VracClient::CVO_REPARTITION_100_NEGO;
        }

        return VracClient::CVO_REPARTITION_50_50;
    }

    public function validate($options = array()) {
        if (isset($options['isTeledeclarationMode']) && $options['isTeledeclarationMode']) {
            $this->valide->statut = VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE;
            if ($this->acheteur_identifiant == $this->createur_identifiant) {
                $this->valide->add('date_signature_acheteur', date('Y-m-d H:i:s'));
            }
            if ($this->mandataire_identifiant == $this->createur_identifiant) {
                $this->valide->add('date_signature_courtier', date('Y-m-d H:i:s'));
            }
        } else {
            $this->valide->statut = VracClient::STATUS_CONTRAT_NONSOLDE;
        }
        if (!$this->valide->date_saisie) {
            $this->valide->date_saisie = date('Y-m-d H:i:s');
        }

        if (isset($options['identifiant'])) {
            $this->valide->identifiant = $options['identifiant'];
        }
        $this->updateVersementFa();

        $this->update();
    }

    public function getDateCampagne($format = 'd/m/Y') {
        return $this->getDate('date_campagne', $format);
    }

    public function getPeriode() {
        $date = $this->getDateSignature('');
        if ($date)
            return $date;
        return date('Y-m-d');
    }

    public function getDroitCVO() {
        return $this->getConfigProduit()->getDroitCVO($this->getPeriode(), $this->type_transaction);
    }

    public function getRepartitionCVOCoef($identifiant, $date_application = null) {
        if ($date_application && $date_application > '2015-12-31') {
            $isAcheteurInterloire = $this->getAcheteurObject()->isInterLoire($this->produit);
            if ($this->acheteur_identifiant == $identifiant) {
                if ($isAcheteurInterloire) {
                    return 1.0;
                } else {
                    return 0.0;
                }
            }
            if ($this->vendeur_identifiant == $identifiant) {
                if ($isAcheteurInterloire) {
                    return 0.0;
                } else {
                    return 1.0;
                }
            }
        }
        if (($this->acheteur_identifiant == $identifiant || $this->vendeur_identifiant == $identifiant) && $this->cvo_repartition == VracClient::CVO_REPARTITION_50_50) {

            return 0.5;
        }

        if ($this->acheteur_identifiant == $identifiant && $this->cvo_repartition == VracClient::CVO_REPARTITION_100_NEGO) {

            return 1.0;
        }

        if ($this->vendeur_identifiant == $identifiant && $this->cvo_repartition == VracClient::CVO_REPARTITION_100_VITI) {

            return 1.0;
        }

        return 0.0;
    }

    public function getConfigProduit() {
        return $this->getConfig()->get($this->produit);
    }

    public function getVendeurObject() {
        return EtablissementClient::getInstance()->find($this->vendeur_identifiant, acCouchdbClient::HYDRATE_DOCUMENT);
    }

    public function getAcheteurObject() {
        return EtablissementClient::getInstance()->find($this->acheteur_identifiant, acCouchdbClient::HYDRATE_DOCUMENT);
    }

    public function getMandataireObject() {
        return EtablissementClient::getInstance()->find($this->mandataire_identifiant, acCouchdbClient::HYDRATE_DOCUMENT);
    }

    public function getSoussigneObjectById($soussigneId) {
        return EtablissementClient::getInstance()->find($soussigneId, acCouchdbClient::HYDRATE_DOCUMENT);
    }

    public function getCreateurObject() {
        return EtablissementClient::getInstance()->find($this->createur_identifiant, acCouchdbClient::HYDRATE_DOCUMENT);
    }

    public function getNonCreateursArray() {
        $non_createurs = array();
        $non_createurs[$this->vendeur_identifiant] = $this->getVendeurObject();
        if (!$this->mandataire_exist) {
            return $non_createurs;
        }
        if ($this->mandataire_identifiant == $this->createur_identifiant) {
            $non_createurs[$this->acheteur_identifiant] = $this->getAcheteurObject();
            return $non_createurs;
        }
        return $non_createurs;
    }

    private function getDensite() {
        return $this->getConfigProduit()->getDensite();
    }

    public function getConfig() {

        return ConfigurationClient::getConfiguration($this->getDateConfig());
    }

    public function __toString() {

        if ($this->exist("numero_archive") && $this->numero_archive)
            return sprintf("%05d", $this->numero_archive);
        return $this->numero_contrat;
    }

    public function updateVolumesEnleves() {
        $this->volume_enleve = 0;
        $mvts = $this->getMouvementsFromDrmOrSV12ImpactVolumeEnleve();
        foreach ($mvts as $mvt) {
            $volume_enleve = $mvt->volume * -1;
            $this->volume_enleve += $volume_enleve;
        }

        if ($this->volume_propose * 0.9 <= $this->volume_enleve) {
            $this->solder();
        } else {
            $this->desolder();
        }
    }

    public function getMouvementsFromDrmOrSV12ImpactVolumeEnleve(){
        $mvts = array();
        $campagneArray = array();
        $cfClient = ConfigurationClient::getInstance();
        $dateVrac = $this->getDateCampagne("Y-m-d");
        $campagne = $cfClient->getPreviousCampagne($cfClient->buildCampagne($dateVrac));
        $campagneArray[$campagne] = $campagne;
        $currentCampagne = $cfClient->buildCampagne(date("Y-m-d"));
        while($campagne <= $currentCampagne){
            $campagne = $cfClient->getNextCampagne($campagne);
            $campagneArray[$campagne] = $campagne;
        }
        foreach ($campagneArray as $campagne) {
            if(($this->type_transaction == VracClient::TYPE_TRANSACTION_VIN_VRAC) || ($this->type_transaction == VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE)){
                $mvts_drm = DRMMouvementsConsultationView::getInstance()->getMouvementsByEtablissementAndCampagne($this->vendeur_identifiant,$campagne);
                foreach ($mvts_drm as $key => $mvt) {
                    $pos = strpos($mvt->produit_hash, $this->produit);
                    if($mvt->type_hash == "vrac_details" && ($pos !== false) && $mvt->detail_identifiant == $this->_id){
                        $mvts[] = $mvt;
                    }
                }
            }elseif(($this->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS) || ($this->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS)){
                $mvts_sv12 = SV12MouvementsConsultationView::getInstance()->getMouvementsByEtablissementAndCampagne($this->acheteur_identifiant,$campagne);
                foreach ($mvts_sv12 as $key => $mvt) {
                    $pos = strpos($mvt->produit_hash, $this->produit);
                    if($pos !== false && $mvt->detail_identifiant == $this->_id){
                        $mvts[] = $mvt;
                    }
                }
            }
        }
        return $mvts;
    }


    public function isSolde() {
        return $this->valide->statut == VracClient::STATUS_CONTRAT_SOLDE;
    }

    public function solder() {
        $this->valide->statut = VracClient::STATUS_CONTRAT_SOLDE;
    }

    public function desolder() {
        $this->valide->statut = VracClient::STATUS_CONTRAT_NONSOLDE;
    }

    public function isVise() {

        return in_array($this->valide->statut, VracClient::$statuts_vise);
    }

    public function hasPrixVariable() {
        return $this->prix_variable && $this->prix_variable == 1;
    }

    public function hasPrixDefinitif() {

        return $this->_get('prix_unitaire') && $this->_get('prix_unitaire') > 0;
    }

    public function isRaisinMoutNegoHorsIL() {
        $isRaisinMout = (($this->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS) ||
                ($this->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS));
        if (!$isRaisinMout)
            return false;
        $nego = EtablissementClient::getInstance()->findByIdentifiant($this->acheteur_identifiant);
        if ($nego->isRegionIGPValDeLoire()) {
            return false;
        }
        return !$nego->isInterLoire($this->produit);
    }

    public function isVitiRaisinsMoutsTypeVins() {
        return EtablissementClient::getInstance()->find($this->vendeur_identifiant)->raisins_mouts == 'oui' && $this->isVin();
    }

    public function isEnAttenteDOriginal() {
        return $this->isVise() && $this->attente_original;
    }

    public function getMaster() {
        return $this;
    }

    public function isMaster() {
        return true;
    }

    protected function preSave() {
        $this->archivage_document->preSave();
    }

    /*     * * ARCHIVAGE ** */

    public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function isArchivageCanBeSet() {

        return $this->isVise();
    }

    /*     * * FIN ARCHIVAGE ** */

    public function isVin() {

        return in_array($this->type_transaction, VracClient::$types_transaction_vins);
    }

    public function getStockCommercialisable() {
        if (!$this->isVin()) {
            return null;
        }

        $stock = DRMStocksView::getInstance()->getStockFin($this->campagne, $this->getVendeurObject(), $this->produit);
        $volume_restant = VracStocksView::getInstance()->getVolumeRestantVin($this->campagne, $this->getVendeurObject(), $this->produit);

        return $stock - $volume_restant;
    }

    private function convertStringToFloat($q) {
        $qstring = str_replace(',', '.', $q);
        $qfloat = floatval($qstring);
        if (!is_float($qfloat))
            throw new sfException("La valeur $qstring n'est pas un nombre valide");
        return $qfloat;
    }

    public function getCoordonneesVendeur() {
        return $this->getCoordonnees($this->vendeur_identifiant);
    }

    public function getCoordonneesAcheteur() {
        return $this->getCoordonnees($this->acheteur_identifiant);
    }

    public function getCoordonneesMandataire() {
        return $this->getCoordonnees($this->mandataire_identifiant);
    }

    private function isMandatant($mandatant_name) {
        if (!$this->exist('mandatant')) {
            return false;
        }
        foreach ($this->mandatant as $mandatant) {
            if ($mandatant_name == $mandatant) {
                return true;
            }
        }
        return false;
    }

    public function isMandatantAcheteur() {
        return $this->isMandatant('acheteur');
    }

    public function isMandatantVendeur() {
        return $this->isMandatant('vendeur');
    }

    public function getCoordonnees($id_etb) {
        if ($etb = EtablissementClient::getInstance()->retrieveById($id_etb))
            return $etb->getContact();
        $compte = new stdClass();
        $compte->nom_a_afficher = 'Nom Prénom';
        $compte->telephone_bureau = '00 00 00 00 00';
        $compte->telephone_mobile = '00 00 00 00 00';
        $compte->fax = '00 00 00 00 00';
        $compte->email = 'email@email.com';
        return $compte;
    }

    public function getProduitsConfig() {
        $date = (!$this->date_signature) ? date('Y-m-d') : Date::getIsoDateFromFrenchDate($this->date_signature);

        return $this->getConfig()->formatProduits($date, "%format_libelle% (%code_produit%)", array(_ConfigurationDeclaration::ATTRIBUTE_CVO_FACTURABLE));
    }

    public function isProduitIGP() {
        return preg_match("/IGP_VALDELOIRE/", $this->produit);
    }


    public function getQuantite() {
        switch ($this->type_transaction) {
            case VracClient::TYPE_TRANSACTION_VIN_VRAC :
            case VracClient::TYPE_TRANSACTION_MOUTS :
                return $this->jus_quantite;
            case VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE :
                return $this->bouteilles_quantite;
            case VracClient::TYPE_TRANSACTION_RAISINS :
                return $this->raisin_quantite;
            default:
                return null;
        }
    }

    public function getVisa() {
        if ($this->exist('visa')) {
            return $this->visa;
        }
        return "Pas de visa";
    }

    public function getFraisDeGarde() {
        if ($this->exist('enlevement_frais_garde')) {
            return $this->enlevement_frais_garde;
        }
        return "0";
    }

    public function isGenerique() {
        return $this->categorie_vin == VracClient::CATEGORIE_VIN_GENERIQUE;
    }

    public function isDomaine() {
        return $this->categorie_vin == VracClient::CATEGORIE_VIN_DOMAINE;
    }

    public function getMaxEnlevement() {
        if ($this->exist('enlevement_date') && $this->enlevement_date) {
            return $this->enlevement_date;
        }
        if ($this->exist('date_signature') && $this->date_signature) {
            return Date::addDelaiToDate('+1 month', Date::getIsoDateFromFrenchDate($this->date_signature));
        }
        return null;
    }

    public function getResponsableLieu() {
        if ($this->mandataire_exist) {
            return $this->mandataire->commune;
        }
        return $this->acheteur->commune;
    }

    public function isPluriannuel() {
        return ($this->exist('type_contrat') && $this->type_contrat == VracClient::TYPE_CONTRAT_PLURIANNUEL);
    }

    public function getTeledeclarationStatut() {
        if ($this->isVise()) {
            return VracClient::STATUS_CONTRAT_VISE;
        }
        return $this->valide->statut;
    }

    public function getTeledeclarationStatutLabel() {
        return VracClient::$statuts_labels_teledeclaration[$this->valide->statut];
    }

    public function getStatutLabel() {
        return VracClient::$statuts_labels[$this->valide->statut];
    }

    public function isSigneVendeur() {
        return $this->valide->exist('date_signature_vendeur') && $this->valide->date_signature_vendeur;
    }

    public function isSigneAcheteur() {
        return $this->valide->exist('date_signature_acheteur') && $this->valide->date_signature_acheteur;
    }

    public function isSigneCourtier() {
        return $this->valide->exist('date_signature_courtier') && $this->valide->date_signature_courtier;
    }

    public function setEtablissementCreateur($etablissement) {
        if ($etablissement->getSociete()->isCourtier()) {
            $this->setMandataireIdentifiant($etablissement->_id);
            $this->mandataire_exist = true;
        }
        if ($etablissement->getSociete()->isNegociant() || $etablissement->getSociete()->isNegociantPur()) {
            $this->setAcheteurIdentifiant($etablissement->_id);
        }
    }

    public function signatureByEtb($etb) {
        switch ($etb->getFamilleType()) {
            case 'vendeur' :
                if ($etb->identifiant == $this->vendeur_identifiant) {
                    $this->valide->_add('date_signature_vendeur', date('Y-m-d H:i:s'));
                }
                break;
            case 'acheteur' :
                if ($etb->identifiant == $this->acheteur_identifiant) {
                    $this->valide->_add('date_signature_acheteur', date('Y-m-d H:i:s'));
                }
                break;
            case 'mandataire' :
                if ($etb->identifiant == $this->mandataire_identifiant) {
                    $this->valide->_add('date_signature_courtier', date('Y-m-d H:i:s'));
                }
                break;
        }
        return $this->updateStatutForSignatures();
    }

    private function updateStatutForSignatures() {
        $allSignatures = ($this->isSigneVendeur() && $this->isSigneAcheteur());
        if ($this->mandataire_exist) {
            $allSignatures = $allSignatures && $this->isSigneCourtier();
        }
        if ($allSignatures) {
            $this->valide->statut = VracClient::STATUS_CONTRAT_VALIDE;
            $this->updateVersementFa();
            if (!$this->date_signature) {
                $this->date_signature = date('Y-m-d H:i:s');
            }

            if(sfConfig::get('app_vrac_teledeclaration_visa_automatique', true)) {
                $this->createVisa();
            }
        }
        return $allSignatures;
    }

    public function isTeledeclare() {
        return $this->exist('teledeclare') && $this->teledeclare;
    }

    public function updateVersementFa(){
        if ($this->valide->statut == VracClient::STATUS_CONTRAT_BROUILLON ||
            $this->valide->statut == VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE) {
                return false;
        }
        if (!preg_match('/\/[a-z]+\/[a-z]+\/IGP(.*)/', $this->produit)) {
            return false;
        }

        if (!$this->exist('versement_fa') || !$this->versement_fa) {
            $this->add('versement_fa', VracClient::VERSEMENT_FA_NOUVEAU);
            return true;
        }
        if ($this->exist('versement_fa') && $this->versement_fa == VracClient::VERSEMENT_FA_TRANSMIS) {
            $this->versement_fa = VracClient::VERSEMENT_FA_MODIFICATION;
            return true;
        }
        return false;
    }

    public function storeInterlocuteurCommercialInformations($nom, $contact) {
        $email = trim(preg_replace("/\([0-9]+\)/", "", $contact));

        $telephone = null;
        if (preg_match("/\(([0-9]+)\)/", $contact, $matches)) {
            $telephone = $matches[1];
        }

        $this->interlocuteur_commercial->nom = $nom;
        $this->interlocuteur_commercial->email = ($email) ? $email : null;
        if (!$this->interlocuteur_commercial->exist('telephone')) {
            $this->interlocuteur_commercial->add('telephone');
        }
        $this->interlocuteur_commercial->telephone = ($telephone) ? $telephone : null;
    }

    public function getMillesimeLabel() {
        $type_transaction = $this->type_transaction;
        if ($type_transaction &&
                (($type_transaction == VracClient::TYPE_TRANSACTION_RAISINS) || ($type_transaction == VracClient::TYPE_TRANSACTION_MOUTS))) {
            return 'Récolte';
        }
        return 'Millésime';
    }

    public function isSocieteHasSigned($societe) {
        if (!$this->isTeledeclare()) {
            return true;
        }
        $etbsArr = $societe->getEtablissementsObj();
        foreach ($etbsArr as $id => $etbObj) {
            $identifiant = str_replace('ETABLISSEMENT-', '', $id);
            if (($identifiant == $this->acheteur_identifiant) && $this->isSigneAcheteur()) {
                return true;
            }
            if (($identifiant == $this->vendeur_identifiant) && $this->isSigneVendeur()) {
                return true;
            }
            if (($identifiant == $this->mandataire_identifiant) && $this->isSigneCourtier()) {
                return true;
            }
        }
        return false;
    }

    public function getEtbConcerned($societe) {
        $etbs = $societe->getEtablissementsObj();
        foreach ($etbs as $etbId => $etbStruct) {
            $identifiant = str_replace('ETABLISSEMENT-', '', $etbId);
            if ($identifiant == $this->acheteur_identifiant) {
                return $etbStruct->etablissement;
            }
            if ($identifiant == $this->vendeur_identifiant) {
                return $etbStruct->etablissement;
            }
            if ($identifiant == $this->mandataire_identifiant) {
                return $etbStruct->etablissement;
            }
        }
        return null;
    }

    public function isBrouillon() {
        return $this->valide->statut == VracClient::STATUS_CONTRAT_BROUILLON;
    }

    public function isTeledeclarationAnnulable() {
        return !$this->isVise();
    }

    public function isCreateurType($etb_type) {

        if (!$this->exist('createur_identifiant') || !$this->createur_identifiant || !$this->exist('teledeclare') || !$this->teledeclare) {
            return false;
        }
        $etablissement = EtablissementClient::getInstance()->findByIdentifiant($this->createur_identifiant);
        if (!$etablissement) {
            return false;
        }
        return $etablissement->famille === $etb_type;
    }

    public function hasLabel($label)
    {
        return in_array($label,$this->getLabel()->toArray(0,1));

    }

    public function isBio()
    {
        return $this->hasLabel(VracClient::LABEL_AGRICULTURE_BIOLOGIQUE);

    }

    public function hasBioEcocert(){
      return $this->exist("bio_ecocert") && $this->bio_ecocert;
    }

}
