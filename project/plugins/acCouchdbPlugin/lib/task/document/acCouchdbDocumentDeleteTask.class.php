<?php

class acCouchdbDocumentDeleteTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
    ));

    $this->addOptions(array(
      new sfCommandOption('format', null, sfCommandOption::PARAMETER_REQUIRED, 'Format of return (json, php, flatten)', 'json'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'document';
    $this->name             = 'delete';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceCompteStatut|INFO] task does things.
Call it with:

  [php symfony maintenanceCompteStatut|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $doc = acCouchdbManager::getClient()->find($arguments['doc_id'], acCouchdbClient::HYDRATE_JSON);

    if(!$doc) {
      echo sprintf("%s;%s;%s\n", "ERREUR", "Document introuvable", $arguments['doc_id']);
      return;
    }

    acCouchdbManager::getClient()->deleteDoc($doc);

    echo sprintf("%s;%s;%s\n", "INFO", "Document supprimé", $doc->_id.'@'.$doc->_rev);
  }
} 