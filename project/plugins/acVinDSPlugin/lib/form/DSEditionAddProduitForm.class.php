<?php

class DSEditionAddProduitForm extends acCouchdbForm 
{
    protected $_ds = null;
    protected $_interpro = null;
    protected $_config = null;
    protected $_choices_produits;
    
	public function __construct(DS $ds, $options = array(), $CSRFSecret = null) 
	{
		$this->_ds = $ds;
        $this->_interpro = $ds->getInterpro();
        $this->_config = $this->getConfig();
        $defaults = array();
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }
    
    public function configure() 
    {
        $this->setWidgets(array(
            'hashref' => new sfWidgetFormChoice(array('choices' => $this->getChoices())),
        ));
        $this->widgetSchema->setLabels(array(
            'hashref' => 'Produit: ',
        ));

        $this->setValidators(array(
            'hashref' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getProduits())),array('required' => "Aucun produit n'a été saisi !")),
        ));
        $this->widgetSchema->setNameFormat('ds_add_produit[%s]');
    }

    public function getChoices() 
    {
        if (is_null($this->_choices_produits)) {
            $this->_choices_produits = array_merge(array("" => ""),
												   array("nouveau" => $this->getProduits()));
        }
        return $this->_choices_produits;
    }

    public function getProduits() 
    {
        $date = $this->_ds->getFirstDayOfPeriode();
        return $this->_config->formatProduits($date);
    }

    public function addProduit() 
    {
        if (!$this->isValid()) {
            throw $this->getErrorSchema();
        }
        $dsProduit = $this->_ds->addProduit($this->values['hashref']);
        return $dsProduit;
    }
    
    public function getConfig() 
    {
        return ConfigurationClient::getCurrent();
    }
}