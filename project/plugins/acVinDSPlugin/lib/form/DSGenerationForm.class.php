<?php
class DSGenerationForm extends DSGenerationOperateurForm {

    
    public function __construct($defaults = array(), $options = array(), $CSRFSecret = null) {
        parent::__construct($defaults, $options, $CSRFSecret);
    }

    public function configure()
    {
        parent::configure();
        $this->setWidget('regions', new sfWidgetFormChoice(array('choices' => $this->getRegions(), 'multiple' => true, 'expanded' => true, 'default'=>array_keys($this->getRegions()))));
        $this->setWidget('operateur_types', new sfWidgetFormChoice(array('choices' => $this->getOperateurs(), 'multiple' => true, 'expanded' => true, 'default'=>array_keys($this->getOperateurs()))));
         
	$choix = array_keys($this->getRegions());
	$this->setValidator('regions', new sfValidatorChoice(array('choices' => $choix, 'multiple' => true)));
	$this->setValidator('operateur_types', new sfValidatorChoice(array('choices' => array_keys($this->getOperateurs()), 'multiple' => true)));

        $this->widgetSchema->setLabels(array(
            'regions' => 'Sélectionner les régions concernées :',
            'operateur_types' => "Choisir le type d'opérateur :"
        ));
        $this->widgetSchema->setNameFormat('ds_generation[%s]');
    }

    public function getRegions() {
        return EtablissementClient::getRegions();
    }

    public function getOperateurs() {
        return array(EtablissementFamilles::FAMILLE_PRODUCTEUR => EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_NEGOCIANT => EtablissementFamilles::FAMILLE_NEGOCIANT);
    }

}