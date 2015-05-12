<?php
/**
 * Model for Configuration
 *
 */

class Configuration extends BaseConfiguration {

    const DEFAULT_KEY = 'DEFAUT';
    const DEFAULT_DENSITE = "1.3";
  
    public function constructId() {
        $this->set('_id', "CONFIGURATION");
    }

    public function getProduits() {

        return $this->declaration->getProduits();
    }

    public function formatProduits($date = null, $format = "%format_libelle% (%code_produit%)") {

      return $this->declaration->formatProduits($date,null, null, $format);
    }

    public function formatProduitsWithoutCVONeg($format = "%format_libelle% (%code_produit%)") {

      return $this->declaration->formatProduitsWithoutCVONeg(null, null, $format);
    }
    
    private static function normalizeLibelle($libelle) {
      	$libelle = str_ireplace('SAINT-', 'saint ', $libelle);
      	$libelle = preg_replace('/&nbsp;/', ' ', strtolower($libelle));
      	if (!preg_match('/&[^;]+;/', $libelle)) {
			$libelle = html_entity_decode(preg_replace('/&([^;#])[^;]*;/', '\1', htmlentities($libelle, ENT_NOQUOTES, 'UTF-8')));
     	}
      	$libelle = str_replace(array('é', 'è', 'ê'), 'e', $libelle);
      	$libelle = preg_replace('/[^a-z ]/', '', preg_replace('/  */', ' ', preg_replace('/&([a-z])[^;]+;/i', '\1', $libelle)));
     	$libelle = preg_replace('/^\s+/', '', preg_replace('/\s+$/', '', $libelle));

      	return $libelle;
    }

    private function getObjectByLibelle($parent, $libelle, $previous_libelles = null) {
      	$libelle = ($libelle) ? self::normalizeLibelle($libelle) : 'DEFAUT';
      	$obj_id = 'DEFAUT';
      	foreach ( $parent as $obj_key => $obj_obj) {
			if ($libelle == self::normalizeLibelle($obj_obj->getLibelle())) {
  				$obj_id = $obj_key;

			  	break;
			}
      	}
      	$next_libelles = $libelle;
      	if ($previous_libelles) {
			$next_libelles = $previous_libelles.' / '.$libelle;
      	}
      	if (!$parent->exist($obj_id)) {
		  	throw new Exception($next_libelles);
		}

      	return array('obj' => $obj_obj, 'next_libelles' => $next_libelles);
    }

    public function identifyNodeProduct($certification, $genre, $appellation, $mention, $lieu = 'DEFAUT', $couleur = 'DEFAUT', $cepage = 'DEFAUT', $millesime = null) {
      	$hash = $this->identifyProduct($certification, $genre, $appellation, $mention, $lieu, $couleur, $cepage, $millesime);
      	$rwhash = ' ';
      	while ($rwhash != $hash && $rwhash) {
			if ($rwhash != ' ') {
	  			$hash = $rwhash;
	  		}
			$rwhash = preg_replace('/[^\/]*\/DEFAUT\/?$/', '', $hash);
      	}

      	return $hash;
    }

    public function identifyProduct($certification, $genre, $appellation, $mention = 'DEFAULT', $lieu = 'DEFAUT', $couleur = 'DEFAUT', $cepage = 'DEFAUT', $millesime = null) {
      	try {
		$res = $this->getObjectByLibelle($this->declaration->getCertifications(), $certification);
		$res = $this->getObjectByLibelle($res['obj']->getGenres(), $genre, $res['next_libelles']);
		$res = $this->getObjectByLibelle($res['obj']->getAppellations(), $appellation, $res['next_libelles']);
		$res = $this->getObjectByLibelle($res['obj']->getMentions(), $mention, $res['next_libelles']);
		$res = $this->getObjectByLibelle($res['obj']->getLieux(), $lieu, $res['next_libelles']);
		$res = $this->getObjectByLibelle($res['obj']->getCouleurs(), $couleur, $res['next_libelles']);
		$res = $this->getObjectByLibelle($res['obj']->getCepages(), $cepage, $res['next_libelles']);
		} catch(Exception $e) {		
			throw new sfException("Impossible d'indentifier le produit (".$e->getMessage()." [$certification / $genre / $appellation / $mention / $lieu / $couleur / $cepage / $millesime] )");
		}

		return $res['obj']->getHash();
    }

    public function identifyLabels($labels, $separateur = '|') {
      	$label_keys = array();
      	foreach(explode($separateur, $labels) as $l) {
			if ($k = $this->identifyLabel($l)) {
				$label_keys[] = $k;
			}
      	}
      
      	return $label_keys;
    }

    public function identifyLabel($label) {
      	$label = self::normalizeLibelle($label);
      	foreach ($this->labels as $k => $l) {
			if ($label == self::normalizeLibelle($l)) {
	  			
	  			return $k;
	  		}
      	}
      	
      	return false;
    }
    
    public function setLabelCsv($datas) {
    	if ($datas[LabelCsvFile::CSV_LABEL_CODE] && !$this->labels->exist($datas[LabelCsvFile::CSV_LABEL_CODE])) {
    		$this->labels->add($datas[LabelCsvFile::CSV_LABEL_CODE], $datas[LabelCsvFile::CSV_LABEL_LIBELLE]);
    	}
    }
  
   public function getMillesimes() {
        $lastMillesime =  date('Y');
        $result = array();
        for($i=$lastMillesime;$i>=1991;$i--) $result[$i] = $i;

        return $result;
    }

    public function getLabelsLibelles($labels) {
        $libelles = array(); 
        foreach($labels as $key) {
            $libelles[$key] = $this->labels[$key];
        }

        return $libelles;
    }

     public function formatLabelsLibelle($labels, $format = "%la%", $separator = ", ") {
      $libelles = $this->getLabelsLibelles($labels);

      return ConfigurationClient::getInstance()->formatLabelsLibelle($libelles, $format, $separator);
    }
    
    public function updateAlias($hashProduit,$alias) {
        $hashProduitKey = str_replace('/', '-', $hashProduit);
        if(!$this->alias->exist($hashProduitKey))
            $this->alias->add($hashProduitKey,array());
        $pos = count($this->alias->get($hashProduitKey));
        $this->alias->get($hashProduitKey)->add($pos,$alias);
    }

    public function save() {
        parent::save();
        ConfigurationClient::getInstance()->cacheResetCurrent();
    }

    public function prepareCache() {
      $this->loadAllData();
    }
    
}
