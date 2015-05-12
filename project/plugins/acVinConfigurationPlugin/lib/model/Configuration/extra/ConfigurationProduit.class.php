<?php
class ConfigurationProduit
{
	protected static $arborescence = array('certifications', 'genres', 'appellations', 'mentions', 'lieux', 'couleurs', 'cepages');
	protected static $certifications = array('' => '', 'AOP' => 'AOP', 'IGP' => 'IGP', 'VINSSANSIG' => 'SANS IG', 'LIE' => 'LIE', 'MOUTS' => 'MOÛTS');
	protected static $couleurs = array('' => '', 'Rouge' => 'Rouge', 'Blanc' => 'Blanc', 'Rosé' => 'Rosé');
	protected static $genres = array('' => '', 'EFF' => 'Effervescent', 'TRANQ' => 'Tranquilles', 'VDN' => 'Vin doux naturel');
	protected static $mentions = array('' => '');
	protected static $codeCouleurs = array('Rouge' => 'rouge', 'Blanc' => 'blanc', 'Rosé' => 'rose');
	protected static $libellesNoeud = array (
    	'certification' => 'Catégorie',
	    'genre' => 'Genre', 
	    'appellation' => 'Dénomination',
	    'mention' => 'Mention', 
	    'lieu' => 'Lieu', 
	    'couleur' => 'Couleur', 
	    'cepage' => 'Cépage'
    );
	
	protected $datas;
	protected $appellations;
	protected $lieux;
	protected $cepages;
	
	const APPELLATION_KEY = 6;
	const LIEU_KEY = 8;
	const CEPAGE_KEY = 12;
	
	public function __construct($interpro) {
		$this->datas = ConfigurationClient::getInstance()->findProduitsForAdmin($interpro);
		$this->appellations = array('' => '');
		$this->lieux = array('' => '');
		$this->cepages = array('' => '');
		$this->loadDatas();
	}
	
	private function loadDatas() {
    	foreach ($this->datas->rows as $produit) {
    		$hash = $produit->key[8];
    		if ($this->getKey($hash, self::APPELLATION_KEY) != Configuration::DEFAULT_KEY)
    			$this->appellations[$this->getKey($hash, self::APPELLATION_KEY)] = $produit->key[3];
    		if ($this->getKey($hash, self::LIEU_KEY) != Configuration::DEFAULT_KEY)
    			$this->lieux[$this->getKey($hash, self::LIEU_KEY)] = $produit->key[5];
    		if ($this->getKey($hash, self::CEPAGE_KEY) != Configuration::DEFAULT_KEY)
    			$this->cepages[$this->getKey($hash, self::CEPAGE_KEY)] = $produit->key[7];
    	}
    }
    private function getKey($hash, $codeKey) {
    	$hash = explode('/', $hash);
    	return $hash[$codeKey];
    }
    public function getCertifications() {
    	return self::$certifications;
    }
    public function getAppellations() {
    	return $this->appellations;
    }
    public function getLieux() {
    	return $this->lieux;
    }
    public function getGenres() {
    	return self::$genres;
    }
    public function getMentions() {
    	return self::$mentions;
    }
    public function getCouleurs() {
    	return self::$couleurs;
    }
    public function getCepages() {
    	return $this->cepages;
    }
    public static function getArborescence() {
    	return self::$arborescence;
    }
    public static function getCodeCouleurs() {
    	return self::$codeCouleurs;
    }
    public static function getLibellesNoeud() {
    	return self::$libellesNoeud;
    }
    public static function getLibelleNoeud($noeud) {
    	$libelles = self::getLibellesNoeud();
    	return (isset($libelles[$noeud]))? $libelles[$noeud] : $noeud;
    }
}