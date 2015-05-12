<?php

class DSEditionForm extends acCouchdbForm {

    protected $ds = null;

    public function __construct(acCouchdbJson $ds, $defaults = array(), $options = array(), $CSRFSecret = null) {

        $this->ds = $ds;
        $defaults = array();
        foreach ($this->ds->getDeclarations() as $key => $value) {
	  $defaults['volumeStock_'.$key] = $value->stock_declare;
	  $defaults['vci_'.$key] = $value->vci;
	  $defaults['reserveQualitative_'.$key] = $value->reserve_qualitative;
	  if ($value->exist('stock_elaboration')) {
	    $defaults['elaboration_'.$key] = $value->stock_elaboration;
	  }
        }
        $defaults['commentaire'] = $this->ds->commentaire;
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        foreach ($this->ds->declarations as $key => $declaration) {
	  $this->setWidget('volumeStock_' . $key, new sfWidgetFormInputFloat(array(), array('size' => '6')));
	  $this->setValidator('volumeStock_' . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel('volumeStock_' . $key, 'Volume Stock');

	  $this->setWidget('vci_' . $key, new sfWidgetFormInput(array(), array('size' => '6')));
	  $this->setValidator('vci_' . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel('vci_' . $key, 'VCI');

	  $this->setWidget('reserveQualitative_' . $key, new sfWidgetFormInput(array(), array('size' => '6')));	  
	  $this->setValidator('reserveQualitative_' . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel('reserveQualitative_' . $key, 'Reserve qualitative');

	  if ($declaration->hasElaboration()){
	    $this->setWidget('elaboration_' . $key, new sfWidgetFormInput(array(), array('size' => '6')));	  
	    $this->setValidator('elaboration_' . $key, new sfValidatorNumber(array('required' => false)));
	    $this->widgetSchema->setLabel('elaboration_' . $key, 'Reserve qualitative');
	  }
        }
        $this->setWidget('commentaire', new sfWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));
        $this->setValidator('commentaire', new sfValidatorString(array('required' => false)));
        $this->widgetSchema->setLabel('commentaire', 'Commentaires :');

        $this->widgetSchema->setNameFormat('ds[%s]');
    }

    public function doUpdateObject() {
        $values = $this->values;

        foreach ($values as $prodKey => $volumeRev) {

            if ($prodKey == 'commentaire') {
                $this->getDocument()->commentaire = $volumeRev;
            } else {
	      if (substr($prodKey, 0, strlen('volumeStock_')) === 'volumeStock_')
		$this->updateVolumeStock(substr($prodKey,strlen('volumeStock_')), $volumeRev);
	      if (substr($prodKey, 0, strlen('vci_')) === 'vci_')
		$this->updateVCI(substr($prodKey,strlen('vci_')), $volumeRev);
	      if (substr($prodKey, 0, strlen('reserveQualitative_')) === 'reserveQualitative_')
		$this->updateReserveQualitative(substr($prodKey,strlen('reserveQualitative_')), $volumeRev);
	      if (substr($prodKey, 0, strlen('elaboration_')) === 'elaboration_')
		$this->updateElaborationStock(substr($prodKey,strlen('elaboration_')), $volumeRev);
            }
        }
    }

    public function updateVolumeStock($prodKey, $volumeRev) {
        if ($this->getDocument()->declarations->exist($prodKey)) {
            $this->getDocument()->declarations[$prodKey]->stock_declare = $volumeRev;
        }
    }
 
    public function updateElaborationStock($prodKey, $volumeRev) {
      if ($this->getDocument()->declarations->exist($prodKey)) {
	$this->getDocument()->declarations[$prodKey]->add('stock_elaboration', $volumeRev);
      }
    }
       
    public function updateVCI($prodKey, $volumeRev) {
        if ($this->getDocument()->declarations->exist($prodKey)) {
            $this->getDocument()->declarations[$prodKey]->vci = $volumeRev;
        }
    }
    
    public function updateReserveQualitative($prodKey, $volumeRev) {
        if ($this->getDocument()->declarations->exist($prodKey)) {
            $this->getDocument()->declarations[$prodKey]->reserve_qualitative = $volumeRev;
        }
    }

}
