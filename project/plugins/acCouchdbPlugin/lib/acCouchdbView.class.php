<?php

class acCouchdbView 
{
	protected $client = null;
	protected $design = null;
	protected $view = null;

	public function __construct(acCouchdbClient $client, $design, $view) {
		$this->client = $client;
		$this->design = $design;
		$this->view = $view;
	}

	public function getClient() {

		return $client;
	}

}