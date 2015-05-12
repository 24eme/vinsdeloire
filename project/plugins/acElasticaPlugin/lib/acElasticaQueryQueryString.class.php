<?php

class acElasticaQueryQueryString extends Elastica_Query_QueryString {
	
  public function __construct($query_string = null, $default_operator = 'AND') {
    parent::__construct();
    $this->setDefaultOperator($default_operator);
    if ($query_string) {
      $this->setQuery($query_string);
    }
    return $this;
  }
  
}