<?php

class acElasticaFilterAnd extends Elastica_Filter_And {
	
	const FILTER_TYPE = 'and';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
}