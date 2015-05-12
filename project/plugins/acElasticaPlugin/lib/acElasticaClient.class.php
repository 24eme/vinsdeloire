<?php

class acElasticaClient extends Elastica_Client {

  public function __construct($dsn, $dbname) {
    $this->dbname = $dbname;
    if (preg_match('|http://([^:]*):(\d+)|', $dsn, $matches)) {
      return parent::__construct(array(
				'host' => $matches[1],
				'port' => $matches[2]
				));
    }
    throw new sfException("wrong dsn format, check your databases.yml");
  }


  public function getDefaultIndex() {
    $index = $this->getIndex($this->dbname);
    if (! ($index instanceof Elastica_Index)) {
      throw new sfException("could not find ".$this->dbname);
    }
    return $index;
  }
}