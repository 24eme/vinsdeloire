<?php

class documentReplaceValueTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
    ));

    $this->addOptions(array(
      new sfCommandOption('from', null, sfCommandOption::PARAMETER_REQUIRED, 'Valeur à remplacer (Regexp autorisé)'),
      new sfCommandOption('to', null, sfCommandOption::PARAMETER_REQUIRED, 'Valeur de remplacement (Regexp de remplacement autorisé)'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'document';
    $this->name             = 'replace-value';
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

    $value_from = $options['from'];
    $value_to = $options['to'];

    $array = acCouchdbToolsJson::json2FlatenArray($doc);
    $array_replace = array();
    $modified = false;

    foreach($array as $hash => $value) {

      $replace_value = preg_replace('|'.$value_from.'|', $value_to, $value);

      if(gettype($value) == 'integer') {
        $replace_value = (int) $value;
      }

      if(gettype($value) == 'boolean') {
        $replace_value = (bool) $value;
      }

      if(gettype($value) == 'double') {
        $replace_value = (float) $value;
      }

      if($value === null) {
        $replace_value = null;
      }

      if($replace_value != $value) {
        echo "$doc_id;VALUE;$hash;$value;$replace_value\n";
        $modified = true;
      }

      $array_replace[$hash] = $replace_value;
    }

    if(!$modified) {
    
      return;
    }

    $json = acCouchdbToolsJson::flatArray2Json($array_replace);

    acCouchdbManager::getClient()->storeDoc($json);
  }

} 