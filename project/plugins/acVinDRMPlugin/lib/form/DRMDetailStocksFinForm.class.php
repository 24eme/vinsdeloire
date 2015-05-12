<?php

class DRMDetailStocksFinForm  extends acCouchdbObjectForm {

    public function configure() {
	    $configurationDetail = $this->getObject()->getParent()->getConfig();
    	foreach ($configurationDetail->getStocksFin() as $key => $value) {
    		if ($value->readable) {
	    		if (!$value->writable) {
	    			$this->setWidget($key, new sfWidgetFormInputFloat(array(), array('readonly' => 'readonly')));
	    		} else {
	    			$this->setWidget($key, new sfWidgetFormInputFloat());
	    		}
	    		$this->setValidator($key, new sfValidatorNumber(array('required' => false)));
    		}
    	}        
        $this->widgetSchema->setNameFormat('drm_detail_stocks_fin[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}