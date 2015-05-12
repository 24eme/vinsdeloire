<?php

class DRMProduitAjoutForm extends acCouchdbForm 
{
	protected $_choices_produits;
	protected $_label_choices;
    protected $_drm = null;
    protected $_config = null;
    const LABEL_AUTRE_KEY = "AUTRE";

    public function __construct(DRM $drm, _ConfigurationDeclaration $config, $options = array(), $CSRFSecret = null) {
		$this->_drm = $drm;
        $this->_interpro = $drm->getInterpro();
        $this->_config = $config;
        $defaults = array();
        parent::__construct($drm, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidgets(array(
            'hashref' => new sfWidgetFormChoice(array('choices' => $this->getProduits())),
            'label' => new sfWidgetFormChoice(array('expanded' => true, 'multiple' => true,'choices' => $this->getLabels())),
            'label_supplementaire' => new sfWidgetFormInputText(),
            'disponible' => new sfWidgetFormInputFloat(),
        ));
        $this->widgetSchema->setLabels(array(
            'hashref' => 'Produit*: ',
            'label' => 'Label: ',
            'label_supplementaire' => 'Label supplémentaire: ',
        ));

        $this->setValidators(array(
            'hashref' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getProduits())),array('required' => "Aucun produit n'a été saisi !")),
            'label' => new sfValidatorChoice(array('multiple' => true, 'required' => false, 'choices' => array_keys($this->getLabels()))),
            'label_supplementaire' => new sfValidatorString(array('required' => false)),
            'disponible' => new sfValidatorNumber(array('required' => false)),
        ));

        $this->validatorSchema->setPostValidator(new DRMProduitValidator(null, array('drm' => $this->_drm)));
        $this->widgetSchema->setNameFormat('produit_'.$this->_config->getKey().'[%s]');
    }

    public function getDRM() {

        return $this->_drm;
    }

    public function getLabels() 
    {
        $labels = $this->_config->getLabels($this->_interpro->get('_id'));
        $labels[self::LABEL_AUTRE_KEY] = "Autre";

        return $labels;
    }
    
    public function getProduits() {
        if (is_null($this->_choices_produits)) {
            $date = $this->_drm->getFirstDayOfPeriode();
            $this->_choices_produits = array_merge(array("" => ""),
	    $this->_config->formatProduits($date,$this->_interpro->get('_id'), 
            $this->_drm->getDepartement()));
        }

        return $this->_choices_produits;
    }

    public function addProduit() {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }

        $detail = $this->_drm->addProduit($this->values['hashref'], $this->values['label']);
        $detail->label_supplementaire = $this->values['label_supplementaire'];
        $detail->total_debut_mois = 0;
        if ($this->values['disponible']) {
            $detail->total_debut_mois = $this->values['disponible'];
        }

        return $detail;
    }

}