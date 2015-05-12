<?php

class acElasticaFilterQuery extends Elastica_Filter_Query {
	
	const FILTER_TYPE = 'query';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
}