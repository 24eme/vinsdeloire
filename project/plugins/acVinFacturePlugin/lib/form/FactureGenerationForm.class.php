<?php

class FactureGenerationForm extends BaseForm {
    
    public function __construct($defaults = array(), $options = array(), $CSRFSecret = null) {
        $defaults['date_facturation'] = date('d/m/Y');
        $defaults['type_document'] = FactureClient::FACTURE_LIGNE_ORIGINE_TYPE_DRM;
        parent::__construct($defaults, $options, $CSRFSecret);
    }


    public function configure()
    {
        $this->setWidget('type_document', new sfWidgetFormChoice(array('choices' => $this->getTypesDocument())));
        $this->setWidget('date_mouvement', new sfWidgetFormInput());
        $this->setWidget('date_facturation', new sfWidgetFormInput());
        $this->setWidget('message_communication', new sfWidgetFormTextarea());
        
        $this->setValidator('type_document', new sfValidatorChoice(array('choices' => array_keys($this->getTypesDocument()), 'required' => true)));
        $this->setValidator('date_mouvement', new sfValidatorString());
        $this->setValidator('date_facturation', new sfValidatorString());
        $this->setValidator('message_communication', new sfValidatorString(array('required' => false)));
        
        $this->widgetSchema->setLabels(array(
            'type_document' => "Type de document :",
            'message_communication' => 'Cadre de communication :',
            'date_mouvement' => 'Dernière date de prise en compte des mouvements :',
            'date_facturation' => 'Date de facturation :'
        ));
        $this->widgetSchema->setNameFormat('facture_generation[%s]');
    }     

    public function getTypesDocument() {

        return FactureGenerationMasseForm::$types_document;
    }
}
