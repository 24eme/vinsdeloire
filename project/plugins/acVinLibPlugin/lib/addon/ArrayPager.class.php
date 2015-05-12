<?php

class ArrayPager extends sfPager
{
	protected $array = null;
	protected $results = array();
	protected $fill_with_max = null;

	public function __construct($maxPerPage = 10, $fill_with_max = false)
  	{
	    parent::__construct(null, $maxPerPage);
	    $this->setFillWithMax($fill_with_max);
  	}

	public function setArray($array) {
		$this->array = $array;
	}

	public function init() {
		if(is_null($this->array) && !is_array($this->array)) {

			throw new sfException("No array found you need to initialize with the 'setArray' method before");
		}

		$this->resetIterator();

	    $this->setNbResults(count($this->array));

	    if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults())
	    {
	      $this->setLastPage(0);
	      $this->results = array();
	    }
	    else
	    {
	      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
	      $this->prepareResults();
	    }
	}

	public function gotoPage($page) {
		$this->setPage($page);
		$this->prepareResults();
	}

	public function gotoNextPage() {
		$this->gotoPage($this->getPage() + 1);
	}

	protected function prepareResults() {
		$offset = ($this->getPage() - 1) * $this->getMaxPerPage();
		$this->results = array_slice($this->array, $offset, $this->getMaxPerPage());
	    if ($this->getFillWithMax() && count($this->results) > 0 && count($this->results) < $this->getMaxPerPage()) {
	      	for($i = count($this->results); $i < $this->getMaxPerPage(); $i++) {
					$this->results[] = null;
			}
	    }
	} 

	public function getResults() {

		return $this->results;
	}

	public function retrieveObject($offset) {

		return $results[$offset];
	}

	protected function getFillWithMax() {

		return $this->fill_with_max;
	}

	protected function setFillWithMax($fill_with_max) {

		$this->fill_with_max = $fill_with_max;
	}
}


