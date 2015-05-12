<?php

/**
 * Model for DS
 *
 */
class DS extends BaseDS implements InterfaceDeclarantDocument, InterfaceArchivageDocument {

    protected $declarant_document = null;
    protected $archivage_document = null;

    public function  __construct() {
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

    public function constructId() {
        if($this->statut == null) {
            $this->statut = DSClient::STATUT_A_SAISIR;
        }
        $this->set('_id', DSClient::getInstance()->buildId($this->identifiant, $this->periode));
    }
    
    public function getCampagne() {

        return $this->_get('campagne');
    }
    
    public function getFirstDayOfPeriode() {        
       return substr($this->periode, 0,4).'-'.substr($this->periode, 4,2).'-01';
    }

    public function setDateStock($date_stock) {
        $this->date_echeance = Date::getIsoDateFinDeMoisISO($date_stock, 1);
        $this->periode = DSClient::getInstance()->buildPeriode($date_stock);

        return $this->_set('date_stock', $date_stock);
    }

    public function setPeriode($periode) {
        $this->campagne = DSClient::getInstance()->buildCampagne($periode);

        return $this->_set('periode', $periode);
    }

    public function getLastDRM() {
        
        return DRMClient::getInstance()->findLastByIdentifiantAndCampagne($this->identifiant, $this->campagne);
    }

    public function getLastDS() {
        
        return DSClient::getInstance()->findLastByIdentifiant($this->identifiant);
    }

    public function updateProduits() {
	if ($this->getEtablissement()->isViticulteur()) {
	  $drm = $this->getLastDRM();
	  if ($drm) {
	    return $this->updateProduitsFromDRM($drm); 
	  }
	}
	if ($this->getEtablissement()->isNegociant()) {
	  return $this->updateProduitsFromVracs(); 
	}
        $ds = $this->getLastDS();
        if ($ds) {
           return $this->updateProduitsFromDS($ds); 
        }	
    }

    public function addProduit($hash) {
        $config = ConfigurationClient::getCurrent()->get($hash);
        $produit = $this->declarations->add($config->getHashForKey());
        $produit->produit_hash = $config->getHash();
        $produit->updateProduit();

        return $produit;
    }

    protected function updateProduitsFromDRM($drm) {
        $produits = $drm->getProduits();
	    $this->drm_origine = $drm->_id;
        foreach ($produits as $produit) {
            $produitDs = $this->addProduit($produit->getHash());
            $produitDs->stock_initial = $produit->total;
        }
    }

    protected function updateProduitsFromVracs() {
      $hproduits = VracSoussigneIdentifiantView::getInstance()->getProduitHashesFromCampagneAndAcheteur($this->campagne, $this->getEtablissement());
      $hproduits = array_merge($hproduits, VracSoussigneIdentifiantView::getInstance()->getProduitHashesFromCampagneAndAcheteur(ConfigurationClient::getInstance()->getPreviousCampagne($this->campagne), $this->getEtablissement()));
      foreach ($hproduits as $produit) {
	$produitDs = $this->addProduit($produit);
      }
    }

    protected function updateProduitsFromDS($ds) {
        foreach ($ds->declarations as $produit) {
            if (!$produit->isActif()) {
                
                continue;
            }
            $produitDs = $this->addProduit($produit->produit_hash);
        }
    }
    
    public function isStatutValide() {
        return $this->statut === DSClient::STATUT_VALIDE;
    }

    public function isStatutASaisir() {
        return $this->statut === DSClient::STATUT_A_SAISIR;
    }

    public function updateStatut() {
        $this->statut = DSClient::STATUT_VALIDE;
    }

    protected function preSave() {
        $this->archivage_document->preSave();
	$this->updateProduits();
    }

    /*** DECLARANT ***/

    public function getEtablissementObject() {
        return $this->getEtablissement();
    }

    public function storeDeclarant() {
        $this->declarant_document->storeDeclarant();
    }

    /*** FIN DECLARANT ***/

    /*** ARCHIVAGE ***/

     public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function isArchivageCanBeSet() {

        return $this->isStatutValide();
    }

    /*** FIN ARCHIVAGE ***/
    
    public function getDepartement() 
    {
        if($this->declarant->code_postal )  {
          return substr($this->declarant->code_postal, 0, 2);
        }
        return null;
    }

    public function getEtablissement() 
    {
        return EtablissementClient::getInstance()->find($this->identifiant);
    }
    
    public function getInterpro() 
    {
      	if ($this->getEtablissement()) {
         	return $this->getEtablissement()->getInterproObject();
     	}
    }
    
    public function getMaster() {
        return $this;
    }

    public function isMaster(){
        return true;
    }
    
    public function getCoordonneesIL(){
        $configs = sfConfig::get('app_facture_emetteur');
        if (!array_key_exists($this->declarant->region, $configs))
            throw new sfException(sprintf('Config %s not found in app.yml', $this->declarant->region));
        return $configs[$this->declarant->region];
    }
}
