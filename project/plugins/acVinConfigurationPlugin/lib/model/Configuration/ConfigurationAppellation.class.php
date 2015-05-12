<?php
/**
 * Model for ConfigurationAppellation
 *
 */

class ConfigurationAppellation extends BaseConfigurationAppellation {
	
	  const TYPE_NOEUD = 'appellation';

    public function getChildrenNode() {

      return $this->mentions;
    }

    public function getGenre() {

      return $this->getParentNode();
    }

    public function getCertification() {

        return $this->getGenre()->getCertification();
    }

    public function setDonneesCsv($datas) {
      parent::setDonneesCsv($datas);
    	$this->getGenre()->setDonneesCsv($datas);
    	$this->libelle = ($datas[ProduitCsvFile::CSV_PRODUIT_DENOMINATION_LIBELLE])? $datas[ProduitCsvFile::CSV_PRODUIT_DENOMINATION_LIBELLE] : null;
    	$this->code = ($datas[ProduitCsvFile::CSV_PRODUIT_DENOMINATION_CODE])? $datas[ProduitCsvFile::CSV_PRODUIT_DENOMINATION_CODE] : null;
      $this->densite = ($datas[ProduitCsvFile::CSV_PRODUIT_DENSITE])? $datas[ProduitCsvFile::CSV_PRODUIT_DENSITE] : Configuration::DEFAULT_DENSITE;
    	
    	$this->setDroitDouaneCsv($datas, ProduitCsvFile::CSV_PRODUIT_DENOMINATION_CODE_APPLICATIF_DROIT);
    	$this->setDroitCvoCsv($datas, ProduitCsvFile::CSV_PRODUIT_DENOMINATION_CODE_APPLICATIF_DROIT); 
        
    }
    
  	public function getTypeNoeud() {
  		return self::TYPE_NOEUD;
  	}
}
