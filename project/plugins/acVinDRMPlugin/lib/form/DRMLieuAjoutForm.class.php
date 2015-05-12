<?php

class DRMLieuAjoutForm extends acCouchdbForm {

    protected $_interpro = null;
    protected $_drm = null;
    protected $_config = null;
    protected $_choices_produits = null;

    public function __construct(DRM $drm, _ConfigurationDeclaration $config, $options = array(), $CSRFSecret = null) {
        $this->_drm = $drm;
        $this->_interpro = $drm->getInterpro();
        $this->_config = $config;
        $defaults = array();
        parent::__construct($drm, $defaults, $options, $CSRFSecret);
    }

    public function setup() {
        $this->setWidgets(array(
            'hash' => new sfWidgetFormChoice(array('choices' => $this->getProduits(), 'label' => 'Appellation')),
        ));

        $this->setValidators(array(
            'hash' => new sfValidatorChoice(array('choices' => array_keys($this->getProduits()), 'required' => true), 
                                            array('required' => "Aucune appellation n'a été saisi !")),
        ));

        $this->widgetSchema->setNameFormat('drm_lieu_ajout[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

    public function getProduits() {
        if (is_null($this->_choices_produits)) {
            $date = $this->_drm->getFirstDayOfPeriode();
            $lieux_existant = $this->_drm->get($this->_config->getHash())->getLieuxArray();

            $this->_choices_produits = $this->_config->formatProduits($date,$this->_interpro->get('_id'), 
                                                                      $this->_drm->getDepartement());
            foreach($lieux_existant as $lieu) {
                $hash = substr($lieu->getHash(), 1, strlen($lieu->getHash())-1);

                if (array_key_exists($hash, $this->_choices_produits)) {
                    unset($this->_choices_produits[$hash]);
                }
            }

            $this->_choices_produits = array_merge(array("" => ""), 
                                                   $this->_choices_produits);
        }

        return $this->_choices_produits;
    }
    
    public function addLieu() {

        return $this->_drm->getOrAdd($this->values['hash']);
    }

}