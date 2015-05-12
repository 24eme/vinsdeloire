<?php

class EtablissementClient extends acCouchdbClient {
    /**
     *
     * @return EtablissementClient
     */

    const REGION_TOURS = 'TOURS';
    const REGION_ANGERS = 'ANGERS';
    const REGION_NANTES = 'NANTES';
    const REGION_HORSINTERLOIRE = 'HORS_INTERLOIRE';
    const RECETTE_LOCALE = 'RECETTE_LOCALE';
    const TYPE_DR_DRM = 'DRM';
    const TYPE_DR_DRA = 'DRA';
    const TYPE_LIAISON_BAILLEUR = 'BAILLEUR';
    const TYPE_LIAISON_METAYER = 'METAYER';
    const TYPE_LIAISON_ADHERENT = 'ADHERENT'; //pour les cooperateurs
    const TYPE_LIAISON_CONTRAT_INTERNE = 'CONTRAT_INTERNE';

    const STATUT_ACTIF = 'ACTIF'; #'actif';
    const STATUT_SUSPENDU = 'SUSPENDU'; #'suspendu';

    const OUI = 'OUI';
    const NON = 'NON';

    const RELANCE_DS_OUI = self::OUI;
    const RELANCE_DS_NON = self::NON;

    const RAISINS_MOUTS_OUI = self::OUI;
    const RAISINS_MOUTS_NON = self::NON;

    const EXCLUSION_DRM_OUI = self::OUI;
    const EXCLUSION_DRM_NON = self::NON;

    public static $statuts = array(self::STATUT_ACTIF => 'ACTIF',
                                   self::STATUT_SUSPENDU => 'SUSPENDU'); 

    public static function getInstance() {
        return acCouchdbManager::getClient("Etablissement");
    }

    public function createEtablissementFromSociete($societe) {
        $etablissement = new Etablissement();
        $etablissement->id_societe = $societe->_id;
        $etablissement->identifiant = $this->getNextIdentifiantForSociete($societe);
        $famillesSocieteTypes = self::getFamillesSocieteTypesArray();
        $etablissement->famille = $famillesSocieteTypes[$societe->type_societe];
        $etablissement->constructId();
        return $etablissement;
    }

    public function getNextIdentifiantForSociete($societe) {
        $societe_id = $societe->identifiant;
        $etbs = self::getAtSociete($societe_id, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $last_num = 0;
        foreach($etbs as $id) {
            if(!preg_match('/ETABLISSEMENT-[0-9]{6}([0-9]{2})/', $id, $matches)) {
              continue;
            }

            $num = $matches[1];
            if($num > $last_num) {
              $last_num = $num;
            }
        }

        return sprintf("%s%02d", $societe_id, $last_num + 1);
    }

    public function getAtSociete($societe_id, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('ETABLISSEMENT-' . $societe_id . '00')->endkey('ETABLISSEMENT-' . $societe_id . '99')->execute($hydrate);
    }

    public function getViewClient($view) {
        return acCouchdbManager::getView("etablissement", $view, 'Etablissement');
    }
    
    public function findAll(){
        return EtablissementRegionView::getInstance()->findAll();
    }
    
    public function findByFamille($famille) {
        return EtablissementRegionView::getInstance()->findByFamilleAndRegionNonSuspendu($famille);
    }

    public function findByFamillesAndRegions($familles,$regions){
        return EtablissementRegionView::getInstance()->findByFamillesAndRegionsNonSuspendus($familles,$regions,null);
    }
    
    
    /**
     *
     * @param string $login
     * @param integer $hydrate
     * @return Etablissement 
     * @deprecated find()
     */
    public function retrieveById($id, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return parent::find('ETABLISSEMENT-' . $id, $hydrate);
    }

    public function find($id_or_identifiant, $hydrate = self::HYDRATE_DOCUMENT, $force_return_ls = false) {
        
        return parent::find($this->getId($id_or_identifiant), $hydrate, $force_return_ls);
    }

    public function findByCvi($cvi) {
        $rows = EtablissementFindByCviView::getInstance()->findByCvi($cvi);

        if (!count($rows)) {
            return null;
        }

        return $this->find($rows[0]->id);
    }

    public function getId($id_or_identifiant) {
        $id = $id_or_identifiant;
        if (strpos($id_or_identifiant, 'ETABLISSEMENT-') === false) {
            $id = 'ETABLISSEMENT-' . $id_or_identifiant;
        }

        return $id;
    }

    public function getIdentifiant($id_or_identifiant) {

        return $identifiant = str_replace('ETABLISSEMENT-', '', $id_or_identifiant);
    }

    /**
     * 
     * @deprecated find()
     */
    public function findByIdentifiant($identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return parent::find('ETABLISSEMENT-' . $identifiant, $hydrate);
    }

    public function matchFamille($f) {
        if (preg_match('/producteur/i', $f)) {

            return EtablissementFamilles::FAMILLE_PRODUCTEUR;
        }
        if (preg_match('/n.*gociant/i', $f)) {

            return EtablissementFamilles::FAMILLE_NEGOCIANT;
        }
        if (preg_match('/courtier/i', $f)) {

            return EtablissementFamilles::FAMILLE_COURTIER;
        }

        throw new sfException("La famille $f doit être soit producteur soit negociant soit courtier");
    }

    public function matchSousFamille($sf) {
        $sf = KeyInflector::slugify($sf);
        $matches = array("(particuliere|cooperative)" => EtablissementFamilles::SOUS_FAMILLE_CAVE_PARTICULIERE,
            "regional" => EtablissementFamilles::SOUS_FAMILLE_REGIONAL,
            "exterieur" => EtablissementFamilles::SOUS_FAMILLE_EXTERIEUR,
            "etranger" => EtablissementFamilles::SOUS_FAMILLE_ETRANGER,
            "union" => EtablissementFamilles::SOUS_FAMILLE_UNION,
            "vinificateur" => EtablissementFamilles::SOUS_FAMILLE_VINIFICATEUR);
        foreach ($matches as $match => $s) {
            if (preg_match('/' . $match . '/i', $sf)) {
                return $s;
            }
        }

        if (!$sf) {
            return EtablissementFamilles::SOUS_FAMILLE_CAVE_PARTICULIERE;
        }

        throw new sfException('Sous Famille "' . $sf . '" inconnue');
    }

    public static function getFamillesSocieteTypesArray() {
        return array(SocieteClient::SUB_TYPE_VITICULTEUR => EtablissementFamilles::FAMILLE_PRODUCTEUR,
            SocieteClient::SUB_TYPE_NEGOCIANT => EtablissementFamilles::FAMILLE_NEGOCIANT,
            SocieteClient::SUB_TYPE_COURTIER => EtablissementFamilles::FAMILLE_COURTIER);
    }

    public static function getStatuts() {
        return array(self::STATUT_ACTIF => self::STATUT_ACTIF,
            self::STATUT_SUSPENDU => self::STATUT_SUSPENDU);
    }

    public static function getRecettesLocales() {
        return array(self::RECETTE_LOCALE => 'Recette locale');
    }

    public static function getRegionsWithoutHorsInterLoire() {
        return array(self::REGION_TOURS => self::REGION_TOURS,
            self::REGION_ANGERS => self::REGION_ANGERS,
            self::REGION_NANTES => self::REGION_NANTES);
    }

    public static function getRegions() {
        return array_merge(self::getRegionsWithoutHorsInterLoire(), array(self::REGION_HORSINTERLOIRE => self::REGION_HORSINTERLOIRE));
    }

    public static function getTypeDR() {
        return array(self::TYPE_DR_DRM => self::TYPE_DR_DRM,
            self::TYPE_DR_DRA => self::TYPE_DR_DRA);
    }

    public static function listTypeLiaisons() {
      return array_keys(self::getTypesLiaisons());
    }

    public static function getTypesLiaisons() {
        return array(self::TYPE_LIAISON_BAILLEUR => 'A pour bailleur',
            self::TYPE_LIAISON_METAYER => 'A pour métayer',
            self::TYPE_LIAISON_ADHERENT => 'Adhérent de (coop.)',
            self::TYPE_LIAISON_CONTRAT_INTERNE => 'Contrat interne');
    }

    public static function getPrefixForRegion($region) {
        $prefixs = array(self::REGION_TOURS => '1',
            self::REGION_ANGERS => '2',
            self::REGION_NANTES => '3');
        return $prefixs[$region];
    }

}
