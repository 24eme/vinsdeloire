<?php

class DRMProduitLabelForm extends acCouchdbObjectForm 
{
    public function configure() 
    {
        $this->setWidgets(array(
				'labels' => new sfWidgetFormChoice(array('choices' => $this->getLabels(), 'multiple' => true, 'expanded'=>true)),
        ));
        $this->widgetSchema->setLabels(array(
            'labels' => 'Labels: ',
        ));

        $this->setValidators(array(
            'labels' => new sfValidatorChoice(array('required' => false, 'multiple' => true, 'choices' => array_keys($this->getLabels()))),
        ));

        /*$this->validatorSchema->setPostValidator(new DRMProduitValidator(null, array('drm' => $this->_drm)));*/
	$this->widgetSchema->setNameFormat('labels_'.$this->getObject()->getKey().'[%s]');
    }

    public function getLabels() {
      return $this->getConfig()->labels->toArray();
    }

    public function getConfig() {
    	return ConfigurationClient::getCurrent();
    }
}