<?php

class acElasticaQueryMatchAll extends Elastica_Query_MatchAll {
	
	const FILTER_TYPE = 'match_all';
    
	protected function _getBaseName()
    {
        return self::FILTER_TYPE;
    }
  
}