<?php

class ConfigurationProduitsView extends acCouchdbView
{
	const KEY_TYPE_LINE = 0;
	const KEY_INTERPRO_ID = 1;
	const KEY_CERTIFICATION_KEY = 2;
	const KEY_DEPARTEMENT = 3;
	const KEY_LIEU_HASH = 4;
	const KEY_HASH = 5;
	const KEY_CODE = 6;
  const KEY_CODE_PRODUIT = 7;
  const KEY_CODE_COMPTABLE = 8;

	const VALUE_LIBELLE_CERTIFICATION = 0;
	const VALUE_LIBELLE_GENRE = 1;
	const VALUE_LIBELLE_APPELLATION = 2;
	const VALUE_LIBELLE_MENTION = 3;
	const VALUE_LIBELLE_LIEU = 4;
	const VALUE_LIBELLE_COULEUR = 5;
	const VALUE_LIBELLE_CEPAGE = 6;

	const VALUE_CODE_CERTIFICATION = 0;
	const VALUE_CODE_GENRE = 1;
	const VALUE_CODE_APPELLATION = 2;
	const VALUE_CODE_MENTION = 3;
	const VALUE_CODE_LIEU = 4;
	const VALUE_CODE_COULEUR = 5;
	const VALUE_CODE_CEPAGE = 6;

	const TYPE_LINE_PRODUITS = 'produits';
	const TYPE_LINE_LIEUX = 'lieux';
	const TYPE_LINE_LABELS = 'labels';

	public static function getInstance() {

        return acCouchdbManager::getView('configuration', 'produits', 'Configuration');
    }

    public function findProduits() {

    	return $this->client->startkey(array(self::TYPE_LINE_PRODUITS))
              				->endkey(array(self::TYPE_LINE_PRODUITS, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}

  	public function findProduitsByInterpro($interpro) {

  		return $this->client->startkey(array(self::TYPE_LINE_PRODUITS, $interpro))
              				->endkey(array(self::TYPE_LINE_PRODUITS, $interpro, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}

  	public function findProduitsByCertification($interpro, $certification) {
  		return $this->client->startkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification))
              				->endkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}

  	public function findProduitsByCertificationByDepartement($interpro, $certification, $departement) {
		
		return $this->client->startkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement))
              				->endkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}

  	public function nbProduitsByCertificationByDepartement($interpro, $certification, $departement) {
		
		return $this->client->startkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement))
	            			->endkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement, array()))
					    	->reduce(true)
					    	->group_level(4)
              				->getView($this->design, $this->view);
	}

  	public function findProduitsByLieu($interpro, $certification, $departement, $lieu_hash) {

	    if (substr($lieu_hash, 0,1) == "/") {
	      $lieu_hash = substr($lieu_hash, 1,strlen($lieu_hash)-1);
	    }

    	return $this->client->startkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement, $lieu_hash))
              				->endkey(array(self::TYPE_LINE_PRODUITS, $interpro, $certification, $departement, $lieu_hash, array()))
              				->reduce(false)
							->getView($this->design, $this->view);  
	}

  	public function findLieuxByCertification($interpro, $certification, $departement) {

		return $this->client->startkey(array(self::TYPE_LINE_LIEUX, $interpro, $certification, $departement))
              				->endkey(array(self::TYPE_LINE_LIEUX, $interpro, $certification, $departement, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}


  	public function findLabelsByCertification($interpro, $certification) {
    	
    	return $this->client->startkey(array(self::TYPE_LINE_LABELS, $interpro, $certification))
              				->endkey(array(self::TYPE_LINE_LABELS, $interpro, $certification, array()))
              				->reduce(false)
              				->getView($this->design, $this->view);
  	}

  	public function getProduitsLibelles() {
  		$produits = $this->findProduits();
    	$libelles = array();

    	foreach($produits->rows as $item) {
    		$libelles['/'.$item->key[self::KEY_HASH]] = $item->value->libelles;
    	}

    	return $libelles;
  	}

  	public function getProduitsCodes() {
  		$produits = $this->findProduits();
    	$codes = array();

    	foreach($produits->rows as $item) {
    		$codes['/'.$item->key[self::KEY_HASH]] = $item->value->codes;
    	}

    	return $codes;
  	}

  	public function formatLibelles($libelles, $format = "%g% %a% %m% %l% %co% %ce%") {
		$format_index = array('%c%' => self::VALUE_LIBELLE_CERTIFICATION,
		                      '%g%' => self::VALUE_LIBELLE_GENRE,
		                      '%a%' => self::VALUE_LIBELLE_APPELLATION,
		                      '%m%' => self::VALUE_LIBELLE_MENTION,
		                      '%l%' => self::VALUE_LIBELLE_LIEU,
		                      '%co%' => self::VALUE_LIBELLE_COULEUR,
		                      '%ce%' => self::VALUE_LIBELLE_CEPAGE);

		$libelle = $format;

		foreach($format_index as $key => $item) {
		  if (isset($libelles[$item])) {
		    $libelle = str_replace($key, $libelles[$item], $libelle);
		  } else {
		    $libelle = str_replace($key, "", $libelle);
		  }
		}

    $libelle = preg_replace('/ +/', ' ', $libelle);

		return $libelle;
  	}

  	public function formatCodes($codes, $format = "%g%%a%%m%%l%%co%%ce%") {
		$format_index = array('%c%' => self::VALUE_CODE_CERTIFICATION,
		                      '%g%' => self::VALUE_CODE_GENRE,
		                      '%a%' => self::VALUE_CODE_APPELLATION,
		                      '%m%' => self::VALUE_CODE_MENTION,
		                      '%l%' => self::VALUE_CODE_LIEU,
		                      '%co%' => self::VALUE_CODE_COULEUR,
		                      '%ce%' => self::VALUE_CODE_CEPAGE);

		$code = $format;

		foreach($format_index as $key => $item) {
		  if (isset($codes[$item])) {
		    $code = str_replace($key, $codes[$item], $code);
		  } else {
		    $code = str_replace($key, "", $code);
		  }
		}

    $code = preg_replace('/ +/', ' ', $code);

		return $code;
  	}

  	public function formatProduits($produits, $format = "%format_libelle% (%code_produit%)") {
  		$produits_format = array();
  		foreach($produits as $produit) {
  			$produits_format[$produit->key[self::KEY_HASH]] = $this->formatProduit($produit, $format);
        }
        ksort($produits_format);

        return $produits_format;
  	}

  	public function formatProduit($produit, $format = "%g% %a% %m% %l% %co% %ce%") {
        $libelle = $this->formatLibelles($produit->value->libelles, $format);

        $libelle = str_replace(array('%code%', 
                          '%code_produit%', 
                          '%code_comptable%'), 
                    array($produit->key[self::KEY_CODE], 
                          $produit->key[self::KEY_CODE_PRODUIT], 
                          $produit->key[self::KEY_CODE_COMPTABLE]),
                    $libelle);
        $libelle = str_replace("()", "", $libelle);
        $libelle = preg_replace('/ +/', ' ', $libelle);


        return $libelle;
  	}

    public function formatProduitsHashByCodeProduit($produits) {
      $produits_hash = array();
      foreach($produits as $produit) {
        if ($produit->key[self::KEY_CODE_PRODUIT]) {
          $produits_hash[$produit->key[self::KEY_CODE_PRODUIT]] = $produit->key[self::KEY_HASH];
        }
      }
      ksort($produits_hash);

      return $produits_hash;
    }
	
}  