<?php

class acCouchdbSchemaToJsonSchemaTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'Modéle'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'couchdb';
        $this->name = 'schema2json';
        $this->briefDescription = 'Generates a json from schema';
        $this->detailedDescription = <<<EOF
The [generate-schema|INFO] task does things.
Call it with:

  [php symfony generate-schema|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        echo json_encode(acCouchdbManager::getDefinition($arguments['model'])->toArray());
    }

}
