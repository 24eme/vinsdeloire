<?php

class acElasticaFilterRange extends Elastica_Filter_Range {
	
	const FILTER_TYPE = 'range';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
}