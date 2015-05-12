<?php

require_once dirname(__FILE__).'/../../../../test/bootstrap/unit.php';

$configuration = ProjectConfiguration::getApplicationConfiguration('vinsdeloire', 'test', true);
$databaseManager = new sfDatabaseManager($configuration);
$context = sfContext::createInstance($configuration);

$schema = "
DocTest:
  definition:
    fields:
      _id: {  }
      _rev: {  }
      type: {  }
      string: {  }
      float: {  }
      array:
        type: array_collection
        definition:
          fields:
            '*': {  }
      array_hash:
        type: collection
        definition:
          fields:
            '*': {  }
";
acCouchdbManager::setSchema(sfYaml::load($schema));

class DocTest extends acCouchdbDocument {
    public function getDocumentDefinitionModel() {
        
        return 'DocTest';
    }

    public function constructId() {
      $this->_id = 'DOCTEST';
    }
}

acCouchdbManager::getClient()->delete(acCouchdbManager::getClient()->find('DOCTEST'));

$t = new lime_test(0);

$doc = new DocTest();

$doc->save();