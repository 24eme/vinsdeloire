<?php

class VracDetailModificationForm extends acCouchdbObjectForm {

	public function configure() {
        $this->setWidgets(array(
        		'volume' => new sfWidgetFormInputFloat()
        ));
        $this->widgetSchema->setLabels(array(
        		'volume' => 'Volume '
        ));

        $this->setValidators(array(
        		'volume' => new sfValidatorNumber(array('required' => false))
        ));
		$this->widgetSchema->setNameFormat('vrac_detail'.$this->getObject()->getParent()->getParent()->getIdentifiantHTML().'_'.$this->getObject()->getKey().'[%s]');
    }

}