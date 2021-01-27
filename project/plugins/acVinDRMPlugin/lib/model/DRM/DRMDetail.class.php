<?php

/**
 * Model for DRMDetail
 *
 */
class DRMDetail extends BaseDRMDetail {

    public function getConfig() {
        return $this->getParent()->getConfigDetails();
    }


    public function getLibelle($format = "%format_libelle%", $label_separator = ", ") {
        $s = str_replace('&', ' et ', $this->getCepage()->getConfig()->getLibelleFormat($this->get('denomination_complementaire'), $format, $label_separator));
        if ($this->exist('tav') && $this->tav) {
            $s .= " - ".$this->tav."°";
        }
        if ($this->produit_libelle && $this->isDefaultProduit()) {
            $s = $this->produit_libelle;
            if ($this->denomination_complementaire != $this->produit_libelle) {
                $s .= " ".$this->denomination_complementaire;
            }
            $s .= " (Hors Interpro)";
        }
        return $s;
    }

    public function isDefaultProduit() {
        if (!$this->code_inao) {
            return false;
        }
        $hash = DRMImportCsvEdi::getEdiDefaultFromInao($this->code_inao);
        return ($hash) && ($this->getCepage()->getHash() == $hash);
    }


    public function getCode($format = "%g%%a%%m%%l%%co%%ce%") {

        return $this->getCepage()->getConfig()->getCodeFormat($format);
    }

    public function getCodeProduit() {

        return $this->getCepage()->getConfig()->getCodeProduit();
    }

    public function hasLibelleModified() {
    	return ($this->produit_libelle && $this->produit_libelle !== $this->getLibelle())? true : false;
    }

    /**
     *
     * @return DRMCepage
     */
    public function getCepage() {

        return $this->getParent()->getParent();
    }

    /**
     *
     * @return DRMCouleur
     */
    public function getCouleur() {

        return $this->getCepage()->getCouleur();
    }

    /**
     *
     * @return DRMLieu
     */
    public function getLieu() {

        return $this->getCouleur()->getLieu();
    }

    /**
     *
     * @return DRMMention
     */
    public function getMention() {

        return $this->getLieu()->getMention();
    }

    /**
     *
     * @return DRMAppellation
     */
    public function getAppellation() {

        return $this->getLieu()->getAppellation();
    }

    public function hasProduitDetailsWithStockNegatif() {

        return $this->getCepage()->hasProduitDetailsWithStockNegatif();
    }

    public function getGenre() {
        return $this->getAppellation()->getGenre();
    }

    public function getCertification() {
        return $this->getGenre()->getCertification();
    }

    public function getLabelKeyString() {
        if ($this->labels) {
            return implode('|', $this->labels->toArray());
        }

        return '';
    }



    public function getTypeDRM() {

        return $this->getParent()->getTypeDRM();
    }

    public function getTypeDRMLibelle() {

        return $this->getParent()->getTypeDRMLibelle();
    }


    public function getLabelsLibelle($format = "%la%", $label_separator = ", ") {

        return $this->getConfig()->getDocument()->formatLabelsLibelle($this->labels->toArray(), $format, $label_separator);
    }

    public function canSetStockDebutMois() {
       return $this->getDocument()->canSetStockDebutMois();
    }

    public function canSetLabels() {

        return !$this->hasPrecedente();
    }

    public function hasPrecedente() {
        if (!$this->getDocument()->hasPrecedente()) {

            return false;
        }


        return $this->getDocument()->getPrecedente()->exist($this->getHash());
    }

    protected function update($params = array()) {
        parent::update($params);

        $this->total_debut_mois =   ($this->stocks_fin->exist('revendique') && $this->stocks_debut->exist('revendique'))? $this->stocks_debut->revendique : 0.0;

        if ($this->sorties->exist('vrac_details')) {
            $this->sorties->vrac = 0;
            foreach ($this->sorties->vrac_details as $vrac_detail) {
                $this->sorties->vrac+=$vrac_detail->volume;
            }
        }
        if ($this->sorties->exist('export_details')) {
            $this->sorties->export = 0;
            foreach ($this->sorties->export_details as $export_detail) {
                $this->sorties->export+=$export_detail->volume;
            }
        }
        if ($this->sorties->exist('cooperative_details')) {
            $this->sorties->cooperative = 0;
            foreach ($this->sorties->cooperative_details as $cooperative_detail) {
                $this->sorties->cooperative+=$cooperative_detail->volume;
            }
        }
        $hasobs = false;
        foreach($this->entrees as $entree => $v) {
          if ($this->getConfig()->get('entrees')->exist($entree)){
            if (preg_match('/autres-entrees|replacement/', $this->getConfig()->get('entrees')->get($entree)->douane_cat) && $v) {
                $hasobs = true;
                if (!$this->exist('observations')) {
                  $this->add('observations',$entree);
                }
            }
          }
        }
        foreach($this->sorties as $sortie => $v) {
          if ($this->getConfig()->get('sorties')->exist($sortie)){
            if (!preg_match('/details/', $sortie) && preg_match('/autres-/', $this->getConfig()->get('sorties')->get($sortie)->douane_cat) && $v) {
                $hasobs = true;
                if (!$this->exist('observations')) {
                  $this->add('observations',$sortie);
                }
            }
          }
        }
        if (!$hasobs) {
          $this->remove('observations');
        }

        $this->total_entrees = $this->getTotalByKey('entrees');
        $this->total_sorties = $this->getTotalByKey('sorties');
        if($this->stocks_fin->exist('revendique')){
          $this->stocks_fin->revendique = round($this->stocks_debut->revendique + $this->total_entrees - $this->total_sorties, FloatHelper::getInstance()->getMaxDecimalAuthorized());
        }
        if ($this->entrees->exist('recolte')) {
            $this->total_recolte = $this->entrees->recolte;
        }
        if ($this->entrees->exist('revendique')) {
            $this->total_recolte = $this->entrees->revendique;
        }
        $this->total_facturable = 0;
        $this->updateNoeud('entrees', -1);
        $this->updateNoeud('sorties', 1);

        $this->cvo->volume_taxable = $this->total_facturable;

        $this->total = ($this->stocks_fin->exist("revendique"))? $this->stocks_fin->revendique : 0.0;
        if($this->getConfig()->getDocument()->hasDontRevendique() && $this->stocks_fin->exist('dont_revendique')){
          $this->total_revendique = $this->stocks_fin->dont_revendique;
        }
        if(($this->entrees->exist('excedents') && $this->entrees->excedents)
          || ($this->entrees->exist('retourmarchandisesanscvo') && $this->entrees->retourmarchandisesanscvo)
          || ($this->entrees->exist('retourmarchandisetaxees') && $this->entrees->retourmarchandisetaxees)
          || ($this->entrees->exist('retourmarchandisenontaxees') && $this->entrees->retourmarchandisenontaxees)
          || ($this->sorties->exist('destructionperte') && $this->sorties->destructionperte)){
            if (!$this->exist('observations')) {
              $this->add('observations',null);
            }
        }elseif(!$hasobs){
            $this->remove('observations');
        }
        if(($this->entrees->exist('retourmarchandisesanscvo') && $this->entrees->retourmarchandisesanscvo)
          || ($this->entrees->exist('retourmarchandisetaxees') && $this->entrees->retourmarchandisetaxees)
          || ($this->entrees->exist('retourmarchandisenontaxees') && $this->entrees->retourmarchandisenontaxees)
          || ($this->entrees->exist('retourmarchandisetaxeesacquitte') && $this->entrees->retourmarchandisetaxeesacquitte)
          || ($this->entrees->exist('transfertcomptamatierecession') && $this->entrees->transfertcomptamatierecession)) {
            if (!$this->exist('replacement_date')) {
              $this->add('replacement_date',null);
            }
        }else{
          $this->remove('replacement_date');
        }

        if (! $this->stocks_debut->revendique && !$this->hasStockEpuise()) {
            $this->stocks_debut->revendique = 0 ;
        }
    }

    public function setImportableObservations($observations) {
      $this->add('observations', $observations);
    }

    protected function updateNoeud($hash, $coefficient_facturable) {
        foreach ($this->get($hash) as $key => $volume) {
            if (!$this->getConfig()->exist($hash . "/" . $key)) {
                continue;
            }
            $config = $this->getConfig()->get($hash . "/" . $key);

            if ($config->facturable) {
                $this->total_facturable += $volume * $coefficient_facturable;
            }
        }
        $this->total_facturable = round($this->total_facturable, FloatHelper::getInstance()->getMaxDecimalAuthorized());
    }

    private function getTotalByKey($key) {
        $sum = 0;
        foreach ($this->get($key, true) as $k) {
            if (!is_object($k)) {
                $sum += $k;
            }
        }
        return round($sum, FloatHelper::getInstance()->getMaxDecimalAuthorized());
    }

    public function getTotalDebutMois() {
        if (is_null($this->_get('total_debut_mois'))) {
            return 0;
        } else {
            return $this->_get('total_debut_mois');
        }
    }

    public function nbToComplete() {
        return $this->hasMouvementCheck();
    }

    public function nbComplete() {
        return $this->isComplete();
    }

    public function isComplete() {
        return $this->total_entrees > 0 || $this->total_sorties > 0;
    }

    public function getIdentifiantHTML() {
        return strtolower(str_replace($this->getDocument()->declaration->getHash(), '', str_replace('/', '_', preg_replace('|\/[^\/]+\/DEFAUT|', '', $this->getHash()))));
    }

    public function hasContratVrac() {
        $rows = $this->getContratsVrac();
        return count($rows);
    }

    public function getContratsVrac() {
        $contrats = $this->getContratsVracByHash($this->getCepage()->getHash());

        $correspondance_hash = $this->getCepage()->getConfig()->getCorrespondanceHash();
        if ($correspondance_hash) {
            $contrats = array_merge($contrats, $this->getContratsVracByHash($correspondance_hash));
        }

        return $contrats;
    }

    public function getContratsVracByHash($hash) {
        return DRMClient::getInstance()->getContratsFromProduit($this->getDocument()->identifiant, $hash, array(VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE, VracClient::TYPE_TRANSACTION_VIN_VRAC));
    }

    public function isModifiedMother($key) {

        return $this->getDocument()->isModifiedMother($this->getHash(), $key);
    }

    public function getDroitVolume($type) {

        return $this->sommeLignes(DRMDroits::getDroitSorties()) - $this->sommeLignes(DRMDroits::getDroitEntrees());
    }

    protected function init($params = array()) {
        parent::init($params);

        $keepStock = isset($params['keepStock']) ? $params['keepStock'] : true;

        $this->total_debut_mois = ($keepStock) ? $this->total : null;
        $this->total_entrees = null;
        $this->total_sorties = null;
        $this->total = null;
        if($this->exist('observations')){
          $this->remove("observations");
        }
    }

    public function sommeLignes($lines) {
        $sum = 0;
        foreach ($lines as $line) {
            $sum += $this->get($line);
        }
        return round($sum, FloatHelper::getInstance()->getMaxDecimalAuthorized());
    }

    public function hasStockFinDeMoisDRMPrecedente() {
        $result = false;
        $drmPrecedente = $this->getDocument()->getPrecedente();
        if (!$drmPrecedente->isNew()) {
            if ($drmPrecedente->exist($this->getHash())) {
                if ($drmPrecedente->get($this->getHash())->total) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public function hasMouvement() {

        return $this->total_entrees > 0.0000001 || $this->total_sorties > 0.0000001;
    }

    public function hasStockEpuise() {

        return $this->total_debut_mois <= 0.0000001 && $this->total_debut_mois >= -0.0000001  && !$this->hasMouvement() && $this->total <= 0.0000001 && $this->total >= -0.0000001;
    }

    public function isSupprimable() {

        return $this->hasStockEpuise() && !$this->getDocument()->hasVersion() && ($this->getDocument()->periode != "201708");
    }

    public function hasMouvementCheck() {

        return !$this->pas_de_mouvement_check;
    }

    public function getMouvements() {

        return array_replace_recursive($this->getMouvementsByNoeud('entrees'), $this->getMouvementsByNoeud('sorties'));
    }

    public function getMouvementsByNoeud($hash) {
        $mouvements = array();

        foreach ($this->get($hash) as $key => $volume) {
            if ($volume instanceof acCouchdbJson) {

                continue;
            }
            if (!$this->getConfig()->exist($hash . "/" . $key)) {
                continue;
            }
            $mouvement = DRMMouvement::freeInstance($this->getDocument());

            $mouvement->produit_libelle = $this->getLibelle();
            $mouvement->produit_hash = $this->getHash(); //WARNING : ceci change tout je pense
            $mouvement->type_drm = $this->getTypeDRM();
            $mouvement->type_drm_libelle = $this->getTypeDRMLibelle();
            $mouvement->facture = 0;
            $mouvement->region = $this->getDocument()->region;
            $mouvement->cvo = $this->getCVOTaux();
            $mouvement->type_drm = $this->getTypeDRM();
            $mouvement->type_drm_libelle = $this->getTypeDRMLibelle();
            if ($this->getDocument()->isDRMNegociant() ) {
                    $mouvement->facturable = 0;
            }else{
	            $mouvement->facturable = ($this->getConfig()->get($hash . "/" . $key)->facturable && $mouvement->cvo > 0) ? 1 : 0;
            }
            $mouvement->version = $this->getDocument()->getVersion();
            $mouvement->date_version = ($this->getDocument()->valide->date_saisie) ? ($this->getDocument()->valide->date_saisie) : date('Y-m-d');
            if ($this->getDocument()->isDRMNegociant() ) {
                    $mouvement->categorie = FactureClient::FACTURE_LIGNE_MOUVEMENT_TYPE_NEGOCIANT;
            } else {
	            $mouvement->categorie = FactureClient::FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE;
            }

            if ($this->exist($hash . "/" . $key . "_details")) {
                $mouvements = array_replace_recursive($mouvements, $this->get($hash . "/" . $key . "_details")->createMouvements($mouvement));
                continue;
            }

            $mouvement = $this->createMouvement(clone $mouvement, $hash . '/' . $key, $volume);

            if (!$mouvement) {
                continue;
            }

            $mouvements[$this->getDocument()->getIdentifiant()][$mouvement->getMD5Key()] = $mouvement;
        }

        return $mouvements;
    }

    public function createMouvement($mouvement, $hash, $volume) {
        if ($this->getDocument()->hasVersion() && !$this->getDocument()->isModifiedMother($this, $hash)) {
            return null;
        }

        if ($this->getDocument()->hasVersion() && $this->getDocument()->motherExist($this->getHash() . '/' . $hash)) {
            $volume = $volume - $this->getDocument()->motherGet($this->getHash() . '/' . $hash);
        }

        $config = $this->getConfig()->get($hash);
        $volume = $config->mouvement_coefficient * $volume;

        if ($volume == 0) {
            return null;
        }

        $mouvement->type_hash = $hash;
        $mouvement->type_libelle = $config->getLibelle();
        $mouvement->volume = $volume;
        $mouvement->date = $this->getDocument()->getDate();

        return $mouvement;
    }

    public function getCVOTaux() {
        $this->cvo->calcul();

        return $this->cvo->taux;
    }

    public function storeDroits() {
        $this->cvo->taux = null;
        $this->cvo->calcul();
    }

    public function isEdited() {

        return $this->exist('edited') && $this->edited;
    }

    public function hasMovements() {
        if ($this->hasMouvement()) {

            return true;
        }

        return !$this->exist('no_movements') || !$this->no_movements;
    }

    public function updateDroitsDouanes() {
        $droitsNode = $this->getDocument()->getOrAdd('droits')->getOrAdd('douane');
        $cepageConfig = $this->getCepage()->getConfig();
        $genreKey = $this->getGenre()->getKey();

        foreach ($this->getEntrees() as $entreeKey => $entree) {
            $entreeKey = str_replace('_details', '', $entreeKey);
            if (!$this->getConfig()->exist('entrees/' . $entreeKey)) {
                continue;
            }
            $entreeConf = $this->getConfig()->get('entrees/' . $entreeKey);
            $entreeDrm = $this->get('entrees/' . $entreeKey);

            if ($entreeConf->taxable_douane && $entreeDrm && $entreeDrm > 0) {
                $droitsNode->updateDroitDouane($genreKey, $cepageConfig, $entreeDrm, true);
            }
        }
        foreach ($this->getSorties() as $sortieKey => $sortie) {

            $sortieKey = str_replace('_details', '', $sortieKey);
            if (!$this->getConfig()->exist('sorties/' . $sortieKey)) {
                continue;
            }
            $sortieConf = $this->getConfig()->get('sorties/' . $sortieKey);

            $sortieDrm = $this->get('sorties/' . $sortieKey);

            if ($sortieConf->taxable_douane && $sortieDrm && $sortieDrm > 0) {
                $droitsNode->updateDroitDouane($genreKey, $cepageConfig, $sortieDrm, false);
            }
        }
      }

    public function getCodeDouane() {

        if($this->exist("code_inao") && $this->code_inao) {
            return $this->code_inao;
        }
        if ($this->getDocument()->isNegoce() && $this->getCorrespondanceNegoce()) {
            $this->_set('code_inao', $this->getLibelleFiscalNegocePur());

            return $this->_get('code_inao');
        }

        return $this->getCepage()->getConfig()->code_douane;
    }

    public function isPremix() {
        if (preg_match('/premix/i', $this->produit_libelle) || preg_match('/premix/i', $this->denomination_complementaire) || preg_match('/premix/i', $this->code_douane)) {
            return true;
        }
        return false;
    }

    public function isCodeDouaneNonINAO(){
      $inao = $this->_get('code_inao');
      if (!$inao) {
          $inao = $this->getCepage()->getConfig()->code_douane;
      }
      if(!$inao){
        return false;
      }
      if(preg_match('/^[0-9]/', $inao)){
        return false;
      }
      return true;
    }

    public function isCodeDouaneAlcool(){
        return ConfigurationCepage::isCodeDouaneNeedTav($this->getCodeDouane());
    }

    public function isCodeDouanePI(){
        return ConfigurationCepage::isCodeDouanePI($this->getCodeDouane());
    }

    public function getReplacementDate() {
      $d = $this->_get('replacement_date');
      return preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '\3/\2/\1', $d);
    }

    public function setReplacementDate($d) {
      $d = preg_replace('/(\d{2}).(\d{2}).(\d{4})/', '$3-$2-$1', $d);
      return $this->_set('replacement_date', $d);
    }

    public function getReplacementMonth() {
      if(!$this->exist('replacement_date')) return "";
      $d = $this->_get('replacement_date');
      return sprintf('%02d', preg_replace('/.*(-|\/)(\d{2})(-|\/).*/', '\2', $d));
    }
    public function getReplacementYear() {
      if(!$this->exist('replacement_date')) return "";
      $d = $this->_get('replacement_date');
      return preg_replace('/.*(\d{4}).*/', '\1', $d);
    }
    
    public function setDenominationComplementaire($denomination_complementaire){
      $denomChanged = ($this->get('denomination_complementaire') && ($this->get('denomination_complementaire') != $denomination_complementaire));

      if(!$denomChanged){
        $this->_set('denomination_complementaire',$denomination_complementaire);
      }else{
        $oldKey = $this->getKey();
        $parent = $this->getParent();
        $detailNode = clone $this;
        $detailNode->_set('denomination_complementaire',$denomination_complementaire);

        $newKey = $parent->createSHA1Denom($denomination_complementaire);
        $parent->add($newKey,$detailNode);
        $parent->get($oldKey)->delete();
      }
    }

    public function getTav(){
      if(!$this->exist('tav')){
       return false;
      }
      return $this->_get('tav');
    }

    public function getCorrespondanceNegoce()
    {
        if ($this->isCodeDouaneNonINAO()) {
            return $this->getCepage()->getHash();
        }
        $correspondances = sfConfig::get('app_drm_negoce_correspondances_produits');
        if (!is_array($correspondances)) {
            throw new sfException('no correspondances_produits set in configuration app.yml');
        }
        $c = (isset($correspondances[$this->getGenre()->getHash()])) ? $correspondances[$this->getGenre()->getHash()] : null;
        if (!$c) {
            throw new sfException('correspondances_produits not found in configuration app.yml for key '.$this->getGenre()->getHash());
        }
        if (is_array($c)) {
            $keys = array_keys($c);
            $diff1 = array_diff(array('cep', 'sanscep'), $keys);
            $diff2 = array_diff(array('vdlsup', 'vdlinf'), $keys);
            if (!$diff1) {
                return ($this->getCepage()->getKey() == Configuration::DEFAULT_KEY)? $c['sanscep'] : $c['cep'];
            }
            if (!$diff2) {
                return ($this->getTav() > 18)? $c['vdlsup'] : $c['vdlinf'];
            }
            throw new sfException('errors in configuration app.yml for keys '.implode(', ', $keys));
        } else {
            return $c;
        }
    }

    public function getLibelleFiscalNegocePur() {
        $hash = $this->getCorrespondanceNegoce();
        $hash = preg_replace('/.details.DEFAUT$/', '', $hash);
        $p = ConfigurationClient::getCurrent()->get($hash);
        return $p->getCodeDouane();
    }
}
