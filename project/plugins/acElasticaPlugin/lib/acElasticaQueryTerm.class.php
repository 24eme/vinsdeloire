<?php

class acElasticaQueryTerm extends Elastica_Query_Term {
	
	const FILTER_TYPE = 'term';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
  
}