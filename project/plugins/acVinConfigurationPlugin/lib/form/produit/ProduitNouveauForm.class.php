<?php
class ProduitNouveauForm extends sfForm {
	
    protected $produit;
	protected $configuration;
	protected $configurationProduit;
	protected static $configurationNoeud = array('certifications' => 'certification', 'genres' => 'genre', 'appellations' => 'appellation', 'mentions' => 'mention', 'lieux' => 'lieu', 'couleurs' => 'couleur', 'cepages' => 'cepage');

    protected static $noeudPermissif = array('appellation', 'lieu', 'cepage');
	
	
	public function __construct($configuration, $interpro, $defaults = array(), $options = array(), $CSRFSecret = null) {
		$this->configuration = $configuration;
		parent::__construct($defaults, $options, $CSRFSecret);
	}

    public function configure() {

        foreach(self::$configurationNoeud as $name => $noeud) {
           $this->setWidget($name, self::getWidgetKey($noeud)); 
        }

		$this->widgetSchema->setLabels(array(
			'certifications' => 'Clé catégorie: ',
			'genres' => 'Clé genre: ',
			'appellations' => 'Clé dénomination: ',  	
			'mentions' => 'Clé mention: ', 		
			'lieux' => 'Clé lieu: ', 	
			'couleurs' => 'Clé couleur: ', 
			'cepages' => 'Clé cépage: '
		));

        foreach(self::$configurationNoeud as $name => $noeud) {
           $this->setValidator($name, self::getValidatorKey($noeud)); 
        }

        $this->widgetSchema->setNameFormat('produit[%s]');
    }

    public static function getLibelles($noeud) {
        $libelles = array();
        $items = self::getItems($noeud);

        foreach($items as $key => $item) {
            $libelles[$key] = sprintf('%s (%s)', $item->getKey(), $item->getLibelle());
        }

        return $libelles;
    }

    public static function getItems($noeud) {

        return ConfigurationClient::getCurrent()->declaration->getKeys($noeud);
    }

    public static function getWidgetKey($noeud) {
        
        $widget = new sfWidgetFormChoice(array('choices' => self::getLibelles($noeud)));

        if(in_array($noeud, self::$noeudPermissif)) {
            $widget->setAttribute('class', 'autocomplete permissif');
        }

        return $widget;
    }

    public static function getValidatorKey($noeud) {
        $message_required = "La clé est requise (vous pouvez choisir DEFAUT)";
        $message_invalid = "La clé doit uniquement être composé de lettre en majuscule";

        if(in_array($noeud, self::$noeudPermissif)) {
           return new sfValidatorRegex(array('required' => true, 'pattern' => '/^[A-Z]+$/'), array('required' => $message_required,'invalid' => $message_invalid));
        } else {
            return new sfValidatorChoice(array('required' => true, 'choices' => array_keys(self::getLibelles($noeud))));
        }
    }
    
    public function save() {
        $values = $this->getValues();
        $hash = 'declaration';
        $nodes = ConfigurationProduit::getArborescence();
        $exist = true;
        $new_noeud = array();
        foreach ($nodes as $node) {
            $key = $values[$node];
            if(!$this->configuration->get($hash)->get($node)->exist($key)) {
                $exist = false;
                $items = $this->getItems(self::$configurationNoeud[$node]);
                $noeud = $this->configuration->get($hash)->get($node)->add($key);
                if(array_key_exists($key, $items)) {
                    $noeud->libelle = $items[$key]->getLibelle();
                    $noeud->code = $items[$key]->getCode();
                } else {
                    $new_noeud[] = $noeud->getTypeNoeud();
                }
            }
    		$hash .= sprintf("/%s/%s", $node, $key);
        }

        if($exist) {

            throw new sfException("Ce produit existe déjà");
        }

        if(!in_array('cepage', $new_noeud))  {
            $new_noeud[] = 'cepage';
        }

        $this->produit = $this->configuration->get($hash);

        $this->configuration->save();

        return $new_noeud;
    }

    public function getProduit() {

        return $this->produit;
    }
    
}

