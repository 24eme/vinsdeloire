<?php

class DRMClient extends acCouchdbClient {
   
    const CONTRATSPRODUITS_NUMERO_CONTRAT = 0;
    const CONTRATSPRODUITS_ETS_NOM = 1;
    const CONTRATSPRODUITS_VOL_TOTAL = 2;
    const CONTRATSPRODUITS_VOL_ENLEVE = 3;

    const VALIDE_STATUS_EN_COURS = '';
    const VALIDE_STATUS_VALIDEE = 'VALIDEE';
    const VALIDE_STATUS_VALIDEE_ENVOYEE = 'ENVOYEE';
    const VALIDE_STATUS_VALIDEE_RECUE = 'RECUE';

    protected $drm_historiques = array();

    /**
     *
     * @return DRMClient
     */
    public static function getInstance() {
      
        return acCouchdbManager::getClient("DRM");
    }

    public function buildId($identifiant, $periode, $version = null) {

        return 'DRM-'.$identifiant.'-'.$this->buildPeriodeAndVersion($periode, $version);
    }

    public function buildVersion($rectificative, $modificative) {

        return DRM::buildVersion($rectificative, $modificative);
    }

    public function getRectificative($version) {

        return DRM::buildRectificative($version);
    }
    
    public function getModificative($version) {

        return DRM::buildModificative($version);
    }

    public function getPeriodes($campagne) {
      $periodes = array();
      $periode = $this->getPeriodeDebut($campagne);
      while($periode != $this->getPeriodeFin($campagne)) {
        $periodes[] = $periode;
        $periode = $this->getPeriodeSuivante($periode);
      }

      $periodes[] = $periode;

      return $periodes;
    }

    public function buildDate($periode) {
        
        return ConfigurationClient::getInstance()->buildDate($periode);
    }

    public function getPeriodeDebut($campagne) {

        return ConfigurationClient::getInstance()->getPeriodeDebut($campagne);
    }

    public function getPeriodeFin($campagne) {

        return ConfigurationClient::getInstance()->getPeriodeFin($campagne);
    }

    public function buildCampagne($periode) {
      
        return ConfigurationClient::getInstance()->buildCampagneByPeriode($periode);
    }

    public function buildPeriode($annee, $mois) {

        return ConfigurationClient::getInstance()->buildPeriode($annee, $mois);
    }

    public function getCurrentPeriode() {

        return ConfigurationClient::getInstance()->getCurrentPeriode();
    }

    public function buildPeriodeAndVersion($periode, $version) {
        if($version) {
            return sprintf('%s-%s', $periode, $version);
        }

        return $periode;
    }

    public function getAnnee($periode) {

        return ConfigurationClient::getInstance()->getAnnee($periode);
    }

    public function getMois($periode) {

        return ConfigurationClient::getInstance()->getMois($periode);
    }

    public function getPeriodeSuivante($periode) {

        return ConfigurationClient::getInstance()->getPeriodeSuivante($periode);
    }

    public function findLastByIdentifiantAndCampagne($identifiant, $campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $drms = $this->viewByIdentifiantAndCampagne($identifiant, $campagne);

        foreach($drms as $id => $drm) {

            return $this->find($id, $hydrate);
        }

        return null;
    }

    public function findMasterByIdentifiantAndPeriode($identifiant, $periode, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $drms = $this->viewByIdentifiantPeriode($identifiant, $periode);

        foreach($drms as $id => $drm) {

            return $this->find($id, $hydrate);
        }

        return null;
    }
    
    public function getMaster($id) {
        $matches = array();
        $drm_master = null;
        if(preg_match('/^DRM-([0-9]{8})-([0-9]{6})*/', $id, $matches)){
            $identifiant = $matches[1];
            $periode = $matches[2];
            $drm_master = $this->findMasterByIdentifiantAndPeriode($identifiant, $periode);
        }
        if(!$drm_master){
            throw new sfException("La DRM master avec l'id $id n'a pas été trouvée.");
        }
        return $drm_master;
        
    }

    public function getMasterVersionOfRectificative($identifiant, $periode, $version_rectificative) {
        $drms = $this->viewByIdentifiantPeriodeAndVersion($identifiant, $periode, $version_rectificative);

        foreach($drms as $id => $drm) {

            return $drm[3];
        }

        return null;
    }
    
    public function findOrCreateByIdentifiantAndPeriode($identifiant, $periode, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        if ($obj = $this->findMasterByIdentifiantAndPeriode($identifiant, $periode, $hydrate)) {
         
            return $obj;
        }

        $this->getHistorique($identifiant, $periode)->reload();
      
        return $this->createDocByPeriode($identifiant, $periode);
    }

    public function listCampagneByEtablissementId($identifiant) {
      $rows = acCouchdbManager::getClient()
	->group_level(2)
	->startkey(array($identifiant))
	->endkey(array($identifiant, array()))
	->getView("drm", "all")
	->rows;
      $current = ConfigurationClient::getInstance()->getCurrentCampagne();
      $list = array();
      foreach($rows as $r) {
	$c = $r->key[1];
	$list[$c] = $c;
      }
      krsort($list);
      return ConfigurationClient::getInstance()->getCampagneVinicole()->consoliderCampagnesList($list);
    }

    public function viewByIdentifiant($identifiant) {
        $rows = acCouchdbManager::getClient()
            ->startkey(array($identifiant))
              ->endkey(array($identifiant, array()))
              ->reduce(false)
              ->getView("drm", "all")
              ->rows;
      
        $drms = array();

        foreach($rows as $row) {
            $drms[$row->id] = $row->key;
        }
      
        krsort($drms);
      
        return $drms;
    }

    public function viewByIdentifiantAndCampagne($identifiant, $campagne) {
        $rows = acCouchdbManager::getClient()
            ->startkey(array($identifiant, $campagne))
              ->endkey(array($identifiant, $campagne, array()))
              ->reduce(false)
              ->getView("drm", "all")
              ->rows;
      
        $drms = array();
        foreach($rows as $row) {
            $drms[$row->id] = $row->key;
        }
        krsort($drms);
      
        return $drms;
    }

    protected function viewByIdentifiantPeriode($identifiant, $periode) {
        $campagne = $this->buildCampagne($periode);

        $rows = acCouchdbManager::getClient()
            ->startkey(array($identifiant, $campagne, $periode))
              ->endkey(array($identifiant, $campagne, $periode, array()))
              ->reduce(false)
              ->getView("drm", "all")
              ->rows;
      
        $drms = array();

        foreach($rows as $row) {
            $drms[$row->id] = $row->key;
        }
      
        krsort($drms);
      
        return $drms;
    }

    protected function viewByIdentifiantPeriodeAndVersion($identifiant, $periode, $version_rectificative) {
        $campagne = $this->buildCampagne($periode);

        $rows = acCouchdbManager::getClient()
            ->startkey(array($identifiant, $campagne, $periode, $version_rectificative))
              ->endkey(array($identifiant, $campagne, $periode, $this->buildVersion($version_rectificative, 99)))
              ->reduce(false)
              ->getView("drm", "all")
              ->rows;
      
        $drms = array();

        foreach($rows as $row) {
            $drms[$row->id] = $row->key;
        }
      
        krsort($drms);
      
        return $drms;
    }
    
    public function getContratsFromProduit($vendeur_identifiant, $produit,$transaction_types = null)
    {
      if ($transaction_types && !is_array($transaction_types))
	throw new sfException("transaction_types (param 3) must be an array");
      if (!$transaction_types)
	return $this->getContratsFromProduitAndATransaction($vendeur_identifiant, $produit);

      $vracs = array();
      foreach ($transaction_types as $t) {
	$vracs = array_merge($vracs, $this->getContratsFromProduitAndATransaction($vendeur_identifiant, $produit, $t));
      }
      return $vracs;
    }

    public function getContratsFromProduitAndATransaction($vendeur_identifiant, $produit, $type_transaction = null)
    {
      $startkey = array(VracClient::STATUS_CONTRAT_NONSOLDE, $vendeur_identifiant, $produit);
      if ($type_transaction) {
	array_push($startkey, $type_transaction);
      }
      $endkey = $startkey;
      array_push($endkey, array());
      $rows = acCouchdbManager::getClient()
	->startkey($startkey)
	->endkey($endkey)
	->getView("vrac", "contratsFromProduit")
	->rows;
      $vracs = array();
      foreach($rows as $key => $row) {
	$vol_restant = round($row->value[self::CONTRATSPRODUITS_VOL_TOTAL] - $row->value[self::CONTRATSPRODUITS_VOL_ENLEVE], 2);
	$volume = '['.$row->value[self::CONTRATSPRODUITS_VOL_ENLEVE].'/'.$row->value[self::CONTRATSPRODUITS_VOL_TOTAL].']';
	$volume = ($row->value[self::CONTRATSPRODUITS_VOL_ENLEVE]=='')? '[0/'.$row->value[self::CONTRATSPRODUITS_VOL_TOTAL].']' : $volume;
	$vracs[VracClient::getInstance()->getId($row->id)] = $row->value[self::CONTRATSPRODUITS_ETS_NOM].
	  ' - '.$row->value[self::CONTRATSPRODUITS_NUMERO_CONTRAT].' - '.
	  $vol_restant.' hl '.
	  $volume;
      }
      return $vracs;       
    }
 
    public function findProduits() {
        return $this->startkey(array("produit"))
                ->endkey(array("produit", array()))->getView('drm', 'produits');
    }
  
    public function getAllProduits() {
        $produits = $this->findProduits()->rows;
        $result = array();
        foreach ($produits as $produit) {
          	$result[] = $produit->key[1];
        }
        
        return $result;
    }

    public function getHistorique($identifiant, $campagne_or_periode) {
        $campagne = $campagne_or_periode;

        if(preg_match('/^[0-9]{6}$/', $campagne_or_periode)) {
            $campagne = $this->buildCampagne($campagne_or_periode);
        }

        if (!array_key_exists($identifiant.$campagne, $this->drm_historiques)) {

            $this->drm_historiques[$identifiant.$campagne] = new DRMHistorique($identifiant, $campagne);
        }

        return $this->drm_historiques[$identifiant.$campagne];
    }

    public function createDoc($identifiant, $periode = null) {
        if (!$periode) {
            $periode = $this->getCurrentPeriode();
            $last_drm = $this->getHistorique($identifiant, $periode)->getLastDRM();
            if ($last_drm) {
                $periode = $this->getPeriodeSuivante($last_drm->periode);
            }
        }

        return $this->createDocByPeriode($identifiant, $periode);
    }

    public function createDocByPeriode($identifiant, $periode)
    {
        $prev_drm = $this->getHistorique($identifiant, $periode)->getPrevious($periode);
        $next_drm = $this->getHistorique($identifiant, $periode)->getNext($periode);

        if ($prev_drm) {
            
            return $prev_drm->generateSuivanteByPeriode($periode);
        } elseif ($next_drm) {
            
            return $next_drm->generateSuivanteByPeriode($periode);
        }

        $drm = new DRM();
        $drm->identifiant = $identifiant;
        $drm->periode = $periode;

        $ds = DSClient::getInstance()->findLastByIdentifiant($identifiant);
        if ($ds) {
            $drm->generateByDS($ds);
        }

        return $drm;
    }

    public function generateVersionCascade($drm) {
        if (!$drm->needNextVersion()) {

            return array();
        }

        $drm_version_suivante = $drm->generateNextVersion();

        if (!$drm_version_suivante) { 
            return array();
        }

        $drm_version_suivante->save();

        return array_merge(array($drm_version_suivante->get('_id')),
                           $this->generateVersionCascade($drm_version_suivante));
    }
    
    public function getLibelleFromId($id) {
        if(!$id) {
            return null;
        }

        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Orthographe','Date'));
        $origineLibelle = 'DRM de';
        $drmSplited = explode('-', $id);
        $periode = $drmSplited[2];
        $annee = substr($periode, 0,4);
        $mois = substr($periode, 4,2);
        $date = $annee.'-'.$mois.'-01';
        $df = format_date($date,'MMMM yyyy','fr_FR');
        return elision($origineLibelle,$df);
    }
}
