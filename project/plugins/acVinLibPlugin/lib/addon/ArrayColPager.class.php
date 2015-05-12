<?php

class ArrayColPager extends ArrayPager
{
	protected $nb_col;
	protected $max_per_col;

	public function __construct($nbCol = 3, $maxPerCol = 10, $fill_with_max = false)
  	{
	    parent::__construct($nbCol * $maxPerCol, $fill_with_max);
	    $this->nb_col = $nbCol;
	    $this->max_per_col = $maxPerCol;
  	}

	protected function prepareResults() {
		$offset = ($this->getPage() - 1) * $this->getMaxPerPage();
		$this->results = array_slice($this->array, $offset, $this->getMaxPerPage());
	    if ($this->getFillWithMax() && count($this->results) > 0 && count($this->results) < $this->getMaxPerPage()) {
	      	for($i = count($this->results); $i < $this->getMaxPerPage(); $i++) {
					$this->results[] = null;
			}
	    }
	    $result = array();
	    $counter = 0;
	    $currentCol = 0;
	    foreach ($this->results as $k => $v) {
	    	$result[$currentCol][$k] = $v;
	    	$counter++;
	    	if ($counter == $this->max_per_col) {
	    		$counter = 0;
	    		$currentCol++;
	    	}
	    }
	    $final = array();
	    for ($i=0; $i<$this->max_per_col; $i++) {
		    foreach ($result as $ind => $subArray) {
		    	list($k, $v) = each($subArray);
		    	$final[$k] = $v;
		    	unset($result[$ind][$k]);
		    }
	    }
	    $this->results = $final;
	}
}


