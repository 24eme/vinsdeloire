<?php
/**
 * Inheritance tree class _ConfigurationDeclaration
 *
 */

abstract class _ConfigurationDeclaration extends acCouchdbDocumentTree {

    protected $libelles = null;
    protected $codes = null;
    protected $produits = null;
    protected $produits_with_negCVO = null;
    protected $format_produits = array();
    protected $format_produits_with_negCVO = array();
    protected $libelle_format = array();

	protected function loadAllData() {
		parent::loadAllData();
                $this->getProduitsWithCVONeg();
                $this->getLibelles();
                $this->getCodes();
                $this->formatProduitsWithCVONeg();
                $this->formatProduitsWithCVONeg(null, null, "%format_libelle%");
                $this->formatProduitsWithCVONeg(null, null, "%format_libelle% %la%");
  }

  abstract public function getChildrenNode();

  public function getParentNode() {
		$parent = $this->getParent()->getParent();
		if (!$parent instanceof _ConfigurationDeclaration) {

			throw new sfException('Noeud racine atteint');
		} else {

			return $this->getParent()->getParent();
		}
	}

  public function getProduitsWithCVONeg($interpro = null, $departement = null) {       
    if(is_null($this->produits_with_negCVO)) {
      $this->produits_with_negCVO = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $this->produits_with_negCVO = array_merge($this->produits_with_negCVO, $item->getProduitsWithCVONeg());
      }
    }
    return $this->produits_with_negCVO;
  }
  
    public function getProduits($date = null, $interpro = null, $departement = null) {
        if(!$date) {
            $date = date('Y-m-d');        
        }
        if(is_null($this->produits)) {
            $produits_with_negCVO = $this->getProduitsWithCVONeg($interpro, $departement);
            foreach($produits_with_negCVO as $hash => $item) {
                try{
                    $droit_produit = $item->getDroitCVO($date);
                    $cvo_produit = $droit_produit->getTaux();
                } catch (Exception $ex) {
                    $cvo_produit = 0;
                }
             if($cvo_produit > 0){
                 $this->produits[$hash] = $item;
             }
          }
    }    
    return $this->produits;
  }
  
  

    public function getLibelles() {
            if(is_null($this->libelles)) {
                            $this->libelles = array_merge($this->getParentNode()->getLibelles(), 
                                                              array($this->libelle));
            }

            return $this->libelles;
    }

    public function getCodes() {
            if(is_null($this->codes)) {
                            $this->codes = array_merge($this->getParentNode()->getCodes(), 
                                                              array($this->code));
            }

            return $this->codes;
    }
        
  public function getProduitsHashByCodeDouaneWithCVONeg($interpro) {
      $produits_with_negCVO = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $produits_with_negCVO = array_merge($item->getProduitsHashByCodeDouaneWithCVONeg($interpro),$produits_with_negCVO);
      }

      return $produits_with_negCVO;
  }
  
  public function getProduitsHashByCodeDouane($date,$interpro) {
      $produits_with_negCVO = $this->getProduitsHashByCodeDouaneWithCVONeg($interpro);
      $produitsHashByCodeDouane = array();
      $produits_hashs = array_keys($this->getProduits($date,$interpro));
      foreach($produits_with_negCVO as $pos => $hash) {  
         if(in_array($hash,  $produits_hashs)){
             $produitsHashByCodeDouane[$pos] = $hash;
         }
    }    
    return $produitsHashByCodeDouane;
  }
        
  public function getCodeDouane() {
    if (!$this->_get('code_douane')) {

      return $this->getParentNode()->getCodeDouane();
    }

    return $this->_get('code_douane');
  }    

  public function getCodeProduit() {
    if (!$this->_get('code_produit')) {

      return $this->getParentNode()->getCodeProduit();
    }

    return $this->_get('code_produit');
  }

    public function getCodeComptable() {
      if (!$this->_get('code_comptable')) {

        return $this->getParentNode()->getCodeComptable();
      }    
      
      return $this->_get('code_comptable');
    }

    public function getFormatLibelleCalcule() {
      if (!$this->getFormatLibelle()) {

        return $this->getParentNode()->getFormatLibelleCalcule();
      }    
      
      return $this->getFormatLibelle();
    }

    public function getFormatLibelleDefinitionNoeud() {
      if ($this->getFormatLibelle()) {

        return $this;
      }    
      
      return $this->getParentNode()->getFormatLibelleDefinitionNoeud();
    }
  
  
    public function getDensite(){
      if (!$this->exist('densite') || !$this->_get('densite')) {
        try {
          
          return $this->getParentNode()->getDensite();
        } catch (Exception $e) {

          return null;
        }
      }    
    
      return $this->_get('densite');
    }

	 public function getLibelleFormat($labels = array(), $format = "%format_libelle%", $label_separator = ", ") {
      if(!array_key_exists($format, $this->libelle_format)) {
        $format_libelle = $this->getFormatLibelleCalcule();
        $format = str_replace("%format_libelle%", $format_libelle, $format);
      	$libelle = $this->formatProduitLibelle($format);
      	$libelle = $this->getDocument()->formatLabelsLibelle($labels, $libelle, $label_separator);

      	$this->libelle_format[$format] = trim($libelle);
      }

      return $this->libelle_format[$format];
  	}

    public function formatProduitLibelle($format = "%g% %a% %m% %l% %co% %ce%") {
        $libelle = ConfigurationClient::getInstance()->formatLibelles($this->getLibelles(), $format);

        $libelle = str_replace(array('%code%', 
                          '%code_produit%', 
                          '%code_comptable%'), 
                    array($this->getCodeFormat(), 
                          $this->getCodeProduit(), 
                          $this->getCodeComptable()),
                    $libelle);
        $libelle = str_replace("()", "", $libelle);
        $libelle = preg_replace('/ +/', ' ', $libelle);


        return $libelle;
    }

  	public function getCodeFormat($format = "%g%%a%%m%%l%%co%%ce%") {

  		return ConfigurationClient::getInstance()->formatCodes($this->getCodes(), $format);
  	}

    public function getDroitCVO($date, $interpro = "INTERPRO-inter-loire") {
      
  	  return $this->getDroits($interpro)->get(ConfigurationDroits::CODE_CVO)->getCurrentDroit($date);
  	}
	
    public function getDroits($interpro) {
      $droitsable = $this;
      while (!$droitsable->hasDroits()) {
	      $droitsable = $droitsable->getParent()->getParent();
      }
      return $droitsable->interpro->getOrAdd($interpro)->droits;
    }

	public function setLabelCsv($datas) {
    	$labels = $this->interpro->getOrAdd('INTERPRO-'.strtolower($datas[LabelCsvFile::CSV_LABEL_INTERPRO]))->labels;
    	$canInsert = true;
    	foreach ($labels as $label) {
    		if ($label == $datas[LabelCsvFile::CSV_LABEL_CODE]) {
    			$canInsert = false;
    			break;
    		}
    	}
    	if ($canInsert) {
	    	$labels->add(null, $datas[LabelCsvFile::CSV_LABEL_CODE]);
    	}
    }

    protected function setDepartementCsv($datas) {
    	if (!array_key_exists(ProduitCsvFile::CSV_PRODUIT_DEPARTEMENTS, $datas) || !$datas[ProduitCsvFile::CSV_PRODUIT_DEPARTEMENTS]) {

    		$this->departements = array();

    		return;
    	}

    	$this->departements = explode(',', $datas[ProduitCsvFile::CSV_PRODUIT_DEPARTEMENTS]);
    }

    protected function setDroitDouaneCsv($datas, $code_applicatif) {

    	if (!array_key_exists(ProduitCsvFile::CSV_PRODUIT_DOUANE_NOEUD, $datas) || $code_applicatif != $datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_NOEUD]) {

    		return;
    	}

    	$droits = $this->getDroits('INTERPRO-'.strtolower($datas[ProduitCsvFile::CSV_PRODUIT_INTERPRO]));
    	$date = ($datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_DATE])? $datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_DATE] : '1900-01-01';
    	$taux = ($datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_TAXE])? $this->castFloat($datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_TAXE]) : null;
    	$code = ($datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_CODE])? $datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_CODE] : null;
    	$libelle = ($datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_LIBELLE])? $datas[ProduitCsvFile::CSV_PRODUIT_DOUANE_LIBELLE] : null;
    	$canInsert = true;
    	foreach ($droits->douane as $droit) {
    		if ($droit->date == $date && $droit->taux == $taux && $droit->code == $code) {
    			$canInsert = false;
    			break;
    		}
    	}
    	if ($canInsert) {
	    	$droits = $droits->douane->add();
	    	$droits->date = $date;
	    	$droits->taux = $taux;
	    	$droits->code = $code;
	    	$droits->libelle = $libelle;
    	}
    }
    
    protected function setDroitCvoCsv($datas, $code_applicatif) {

      if (!isset($datas[ProduitCsvFile::CSV_PRODUIT_CVO_NOEUD]) || !$datas[ProduitCsvFile::CSV_PRODUIT_CVO_TAXE] || $code_applicatif != $datas[ProduitCsvFile::CSV_PRODUIT_CVO_NOEUD]) {

    		return;
    	}

    	$droits = $this->getDroits('INTERPRO-'.strtolower($datas[ProduitCsvFile::CSV_PRODUIT_INTERPRO]));
    	$date = ($datas[ProduitCsvFile::CSV_PRODUIT_CVO_DATE])? $datas[ProduitCsvFile::CSV_PRODUIT_CVO_DATE] : '1900-01-01';
    	$taux = ($datas[ProduitCsvFile::CSV_PRODUIT_CVO_TAXE])? $this->castFloat($datas[ProduitCsvFile::CSV_PRODUIT_CVO_TAXE]) : null;
    	$code = ConfigurationDroits::CODE_CVO;
    	$libelle = ConfigurationDroits::LIBELLE_CVO;
    	$canInsert = true;
    	foreach ($droits->cvo as $droit) {
    		if ($droit->date == $date && $droit->code == $code) {
    			$canInsert = false;
    			break;
    		}
    	}
    	if ($canInsert) {
	    	$droits = $droits->cvo->add();
	    	$droits->date = $date;
	    	$droits->taux = $taux;
	    	$droits->code = $code;
	    	$droits->libelle = $libelle;
    	}
    }
    
    protected function castFloat($float) {
    	return floatval(str_replace(',', '.', $float));
    }

    public function formatProduitsWithCVONeg($interpro = null, $departement = null, $format = "%format_libelle% (%code_produit%)") {
        if(!array_key_exists($format, $this->format_produits_with_negCVO)) {
          $produits_with_negCVO = $this->getProduitsWithCVONeg();
          $this->format_produits_with_negCVO[$format] = array();
          foreach($produits_with_negCVO as $hash => $produit) {
            $this->format_produits_with_negCVO[$format][$hash] = $produit->getLibelleFormat(array(), $format);
          }
        }

        return $this->format_produits_with_negCVO[$format];
    }
    
    public function formatProduits($date = null,$interpro = null, $departement = null, $format = "%format_libelle% (%code_produit%)") {
        if(!$date){
            $date = date('Y-d-m');
        }
        if(!array_key_exists($date, $this->format_produits)){
            $this->format_produits[$date] = array();
        }
        if(!array_key_exists($format, $this->format_produits[$date])) {
          $produits = $this->getProduits($date,'INTERPRO-inter-loire',$departement);
          $this->format_produits[$date][$format] = array();
          foreach($produits as $hash => $produit) {
            $this->format_produits[$date][$format][$hash] = $produit->getLibelleFormat(array(), $format);
          }
        }
        return $this->format_produits[$date][$format];
    }

    public function getLabels($interpro) {
      
      throw new sfException("The method \"getLabels\" is not defined");
    }

    public function setDonneesCsv($datas) {
      if ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_PRODUIT_NOEUD] == $this->getTypeNoeud()) {
        $this->code_produit = ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_PRODUIT])? $datas[ProduitCsvFile::CSV_PRODUIT_CODE_PRODUIT] : null;
      }

      if ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_COMPTABLE_NOEUD] == $this->getTypeNoeud()) {
        $this->code_comptable = ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_COMPTABLE])? $datas[ProduitCsvFile::CSV_PRODUIT_CODE_COMPTABLE] : null;
      }

      if ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_DOUANE_NOEUD] == $this->getTypeNoeud()) {
        $this->code_douane = ($datas[ProduitCsvFile::CSV_PRODUIT_CODE_DOUANE])? $datas[ProduitCsvFile::CSV_PRODUIT_CODE_DOUANE] : null;
      }      
    }

  	public abstract function getTypeNoeud();

  	public function getDetailConfiguration() {
  		try {
			$parent_node = $this->getParentNode();
		} catch (Exception $e) {
			return $this->getDetail();;
		}

  		$details = $this->getParentNode()->getDetailConfiguration();
  		if ($this->exist('detail')) {
  			foreach ($this->detail as $type => $detail) {
  				foreach ($detail as $noeud => $droits) {
  					if ($droits->readable !== null)
  						$details->get($type)->get($noeud)->readable = $droits->readable;
  					if ($droits->writable !== null)
  						$details->get($type)->get($noeud)->writable = $droits->writable;
  				}
  			}
  		}
  		return $details;
  	}

    public function getKeys($noeud) {
      if($noeud == $this->getTypeNoeud()) {

        return array($this->getKey() => $this);
      }

      $items = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $items = array_merge($items, $item->getKeys($noeud));
      }

      return $items;
    }

    public function addInterpro($interpro) 
    {
      if ($this->exist('interpro')) {
        $this->interpro->getOrAdd($interpro);
      }
      return $this->getParentNode()->addInterpro($interpro);
    }

    public function hasDepartements() {
        return false;
    }

    public function hasDroits() {
        return true;
    }

    public function hasLabels() {
        return false;
    }

    public function hasDetails() {
        return false;
    }

    public function hasDroit($type) {
      if(!$this->hasDroits()) {

        return false;
      }
      
      if($type == ConfigurationDroits::DROIT_CVO){
        return true;
      }

      return false;
    }

    public function hasCodes() {
        return false;
    }
}