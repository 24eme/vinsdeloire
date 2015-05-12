<?php
class ProduitDetailForm extends acCouchdbObjectForm {

    public function configure() {    	
    	$choices = array(1 => 'oui', 0 => 'non', '' => 'Non défini');
    	$this->setWidgets(array(
    		'readable' => new sfWidgetFormChoice(array('multiple' => false, 'expanded' => true, 'choices' => $choices, 'renderer_options' => array('formatter' => array($this, 'formatter')))),
    		'writable' => new sfWidgetFormInputHidden()
    	));
    	$this->setValidators(array(
    		'readable' => new sfValidatorChoice(array('multiple' => false, 'choices' => array_keys($choices), 'required' => false)),
    		'writable' => new sfValidatorPass()
    	));
		$this->widgetSchema->setLabels(array(
			'readable' => $this->getOption('libelle', null)
		));
    	
    	$this->setDefaults(array(
    		'writable' => 1
    	));
    	$this->widgetSchema->setNameFormat('[%s]');
    }
    public function formatter($widget, $inputs)
	{
	    $rows = array();
	    foreach ($inputs as $input)
	    {
	      $rows[] = $widget->renderContentTag('div', $input['input'].$this->getOption('label_separator').$input['label'], array('class' => 'radio_form'));
	    }
	
	    return !$rows ? '' : implode($widget->getOption('separator'), $rows);
  	}
    
    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
        if (is_null($values['readable'])) {
        	$this->getObject()->set('readable', null);
        }
    }
}