<?php
class ProduitLabelForm extends sfForm {
	
	private $labels = null;

    public function configure() {    	
    	$this->setWidgets(array(
    		'label' => new sfWidgetFormInputText(),
    		'code' => new sfWidgetFormInputText()
    	));
    	$this->setValidators(array(
    		'label' => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')),
    		'code' => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire'))
    	));
		$this->widgetSchema->setLabels(array(
			'label' => 'Label: ',
			'code' => 'Code: '
		));
    	
    	$this->setDefaults(array(
    		'label' => $this->getLabelByCode($this->getOption('code_label')),
    		'code' => $this->getOption('code_label'),
    	));
    }
    
    private function getLabelByCode($code = null) {
    	if (!$this->labels) {
    		$this->labels = ConfigurationClient::getCurrent()->get('labels');
    	}
    	if (!$code || !$this->labels->exist($code)) {
    		return null;
    	} else {
    		return $this->labels->get($code);
    	}
    }
}