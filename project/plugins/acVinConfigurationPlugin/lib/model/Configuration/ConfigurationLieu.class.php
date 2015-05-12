<?php
/**
 * Model for ConfigurationLieu
 *
 */

class ConfigurationLieu extends BaseConfigurationLieu {
	
	  const TYPE_NOEUD = 'lieu';

    protected function loadAllData() {
        parent::loadAllData();
        $this->hasCepage();
    }

    public function getChildrenNode() {

      return $this->couleurs;
    }

	/**
     *
     * @return ConfigurationAppellation
     */
    public function getMention() {
        
        return $this->getParentNode();
    }

    public function getAppellation() {
        
        return $this->getMention()->getAppellation();
    }

    public function getCertification() {

        return $this->getAppellation()->getCertification();
    }
    
    public function getLabels($interpro) {

        return $this->getCertification()->getLabels($interpro);
    }

    public function hasCepage() {
        return $this->store('has_cepage', array($this, 'hasCepageStore'));
    }

    public function hasCepageStore() {
        foreach($this->couleurs as $couleur) {
            if ($couleur->hasCepage()) {
                return true;
            }
        }
        
        return false;
    }

    public function setDonneesCsv($datas) {
      parent::setDonneesCsv($datas);
      
    	$this->getMention()->setDonneesCsv($datas);
    	$this->libelle = ($datas[ProduitCsvFile::CSV_PRODUIT_LIEU_LIBELLE])? $datas[ProduitCsvFile::CSV_PRODUIT_LIEU_LIBELLE] : null;
    	$this->code = ($datas[ProduitCsvFile::CSV_PRODUIT_LIEU_CODE])? $datas[ProduitCsvFile::CSV_PRODUIT_LIEU_CODE] : null;
    	$this->setDroitDouaneCsv($datas, ProduitCsvFile::CSV_PRODUIT_LIEU_CODE_APPLICATIF_DROIT);
    	$this->setDroitCvoCsv($datas, ProduitCsvFile::CSV_PRODUIT_LIEU_CODE_APPLICATIF_DROIT); 
    	
    	$this->setDepartementCsv($datas);
    }
    
  	public function getTypeNoeud() {
  		return self::TYPE_NOEUD;
  	}
}