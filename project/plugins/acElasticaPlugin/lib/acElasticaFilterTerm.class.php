<?php

class acElasticaFilterTerm extends Elastica_Filter_Term {
	
	const FILTER_TYPE = 'term';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
}