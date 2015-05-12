<?php

class acElasticaFilterNot extends Elastica_Filter_Not {
	
	const FILTER_TYPE = 'not';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
}