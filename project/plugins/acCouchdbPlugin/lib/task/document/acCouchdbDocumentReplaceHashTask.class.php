<?php

class documentRemplacerHashTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
    ));

    $this->addOptions(array(
      new sfCommandOption('from', null, sfCommandOption::PARAMETER_REQUIRED, 'Hash à remplacer (Regexp autorisé)'),
      new sfCommandOption('to', null, sfCommandOption::PARAMETER_REQUIRED, 'Hash de remplacement (Regexp de remplacement autorisé)'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'document';
    $this->name             = 'replace-hash';
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

    $doc_id = $arguments['doc_id'];
    $doc = acCouchdbManager::getClient()->find($arguments['doc_id'], acCouchdbClient::HYDRATE_JSON);

    $hash_from = $options['from'];
    $hash_to = $options['to'];

    $array = acCouchdbToolsJson::json2FlatenArray($doc);
    $array_replace = array();
    $modified = false;

    foreach($array as $hash => $value) {
      $type = gettype($value);
      $replace_hash = preg_replace('|'.$hash_from.'|', $hash_to, $hash);

      if($replace_hash != $hash) {
        echo "$doc_id;HASH;$hash;$replace_hash\n";
        $modified = true;
      }

      $array_replace[$replace_hash] = $value;
    }

    if(!$modified) {
      
      return;
    }

    $json = acCouchdbToolsJson::flatArray2Json($array_replace);

    acCouchdbManager::getClient()->storeDoc($json);
  }

} 