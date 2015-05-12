<?php

class ProduitCsvFile extends CsvFile 
{
  const CSV_PRODUIT_INTERPRO = 0;
  const CSV_PRODUIT_CATEGORIE_LIBELLE = 1;    //CATEGORIE == CERTIFICATION
  const CSV_PRODUIT_CATEGORIE_CODE = 2;       //CATEGORIE == CERTIFICATION
  const CSV_PRODUIT_CATEGORIE_CODE_APPLICATIF_DROIT = 'C';
  const CSV_PRODUIT_GENRE_LIBELLE = 3;
  const CSV_PRODUIT_GENRE_CODE = 4;
  const CSV_PRODUIT_GENRE_CODE_APPLICATIF_DROIT = 'G';
  const CSV_PRODUIT_DENOMINATION_LIBELLE = 5; //DENOMINATION == APPELLATION
  const CSV_PRODUIT_DENOMINATION_CODE = 6;    //DENOMINATION == APPELLATION
  const CSV_PRODUIT_DENOMINATION_CODE_APPLICATIF_DROIT = 'A';
  const CSV_PRODUIT_MENTION_LIBELLE = 7;
  const CSV_PRODUIT_MENTION_CODE = 8;
  const CSV_PRODUIT_MENTION_CODE_APPLICATIF_DROIT = 'M';
  const CSV_PRODUIT_LIEU_LIBELLE = 9;
  const CSV_PRODUIT_LIEU_CODE = 10;
  const CSV_PRODUIT_LIEU_CODE_APPLICATIF_DROIT = 'L';
  const CSV_PRODUIT_COULEUR_LIBELLE = 11;
  const CSV_PRODUIT_COULEUR_CODE = 12;
  const CSV_PRODUIT_COULEUR_CODE_APPLICATIF_DROIT = 'CO';
  const CSV_PRODUIT_CEPAGE_LIBELLE = 13;
  const CSV_PRODUIT_CEPAGE_CODE = 14;
  const CSV_PRODUIT_DEPARTEMENTS = 15;
  const CSV_PRODUIT_DOUANE_CODE = 16;
  const CSV_PRODUIT_DOUANE_LIBELLE = 17;
  const CSV_PRODUIT_DOUANE_TAXE = 18;
  const CSV_PRODUIT_DOUANE_DATE = 19;
  const CSV_PRODUIT_DOUANE_NOEUD = 20; //a *_DROIT value previously defined
  const CSV_PRODUIT_CVO_TAXE = 21;
  const CSV_PRODUIT_CVO_DATE = 22;
  const CSV_PRODUIT_CVO_NOEUD = 23; //a _DROIT value previously defined
  const CSV_PRODUIT_REPLI_ENTREE = 24;
  const CSV_PRODUIT_REPLI_SORTI = 25;
  const CSV_PRODUIT_DECLASSEMENT_ENTREE = 26;
  const CSV_PRODUIT_DECLASSEMENT_SORTI = 27;
  const CSV_PRODUIT_DENSITE = 28;
  const CSV_PRODUIT_LABELS = 29;
  const CSV_PRODUIT_CODE_PRODUIT = 30;
  const CSV_PRODUIT_CODE_PRODUIT_NOEUD = 31;
  const CSV_PRODUIT_CODE_COMPTABLE = 32;
  const CSV_PRODUIT_CODE_COMPTABLE_NOEUD = 33;
  const CSV_PRODUIT_CODE_DOUANE = 34;
  const CSV_PRODUIT_CODE_DOUANE_NOEUD = 35;
  
  protected $config;
  protected $errors;
  
  public function __construct($config, $file) {
    parent::__construct($file);
    $this->config = $config;
  }

  private function getProduit($line) 
  {
  	$hash = 'certifications/'.$this->getKey($line[self::CSV_PRODUIT_CATEGORIE_CODE]).
                '/genres/'.$this->getKey($line[self::CSV_PRODUIT_GENRE_CODE], true).
                '/appellations/'.$this->getKey($line[self::CSV_PRODUIT_DENOMINATION_CODE], true).
                '/mentions/'.$this->getKey($line[self::CSV_PRODUIT_MENTION_CODE], true).
                '/lieux/'.$this->getKey($line[self::CSV_PRODUIT_LIEU_CODE], true).
                '/couleurs/'.strtolower($this->couleurKeyToCode($line[self::CSV_PRODUIT_COULEUR_CODE])).
                '/cepages/'.$this->getKey($line[self::CSV_PRODUIT_CEPAGE_CODE], true);
    return $this->config->declaration->getOrAdd($hash);
  }
  
  private function couleurKeyToCode($key) {
    $correspondances = array(1 => "rouge",
                             2 => "rose",
                             3 => "blanc");

    return $correspondances[$key];
  }
  
  private function getKey($key, $withDefault = false) 
  {
	if ($withDefault) {
  		return ($key)? $key : Configuration::DEFAULT_KEY;
  	} elseif (!$key) {
  		throw new Exception('La clé "'.$key.'" n\'est pas valide');
  	} else {
  		return $key;
  	}
  }

  public function importProduits() 
  {    
    $this->errors = array();
    $csv = $this->getCsv();
    try {
		foreach ($csv as $line) {
			$produit = $this->getProduit($line);
			$produit->setDonneesCsv($line);
			$produit->getCertification()->interpro->add($line[self::CSV_PRODUIT_INTERPRO]);
      	}
    } catch(Execption $e) {
    	$this->errors[] = $e->getMessage();
    }
    return $this->config;
  }

  public function getErrors() 
  {
    return $this->errors;
  }
}