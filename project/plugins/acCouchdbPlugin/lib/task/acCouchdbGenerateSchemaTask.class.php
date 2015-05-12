<?php

class acCouchdbGenerateSchemaTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('id_doc', sfCommandArgument::REQUIRED, 'id du document'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'couchdb';
        $this->name = 'generate-schema';
        $this->briefDescription = 'Generates a schema from a document';
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

        $data = acCouchdbManager::getClient()->getDoc($arguments['id_doc']);

        $schema = array($arguments['id_doc'] => $this->generateDefinition(array(), $data));
        $yml = sfYaml::dump($schema, 200);

        
        if (!file_exists(sfConfig::get('sf_data_dir').'/'.'generate-schema')) {
            mkdir(sfConfig::get('sf_data_dir').'/generate-schema');
        }

        $filename = sfConfig::get('sf_data_dir').'/generate-schema/'.$arguments['id_doc'].'.yml';

        if (file_exists($filename)) {
            unlink($filename);
        }

        file_put_contents($filename, $yml);

        $this->logSection('schema created', $filename);

        //$this->log(print_r($schema));
        // add your code here
    }

    protected function generateDefinition(&$definition, $data, $multiple = false) {
        $definition['definition'] = array();
        $this->generateFields($definition['definition'], $data, $multiple);
        return $definition;
    }

    protected function generateFields(&$definition, $data_fields, $multiple = false) {
        foreach ($data_fields as $key => $data_field) {
            if ($multiple) {
                $this->generateField($definition['fields'], '*', $data_field);
                break;
            } else {
                $this->generateField($definition['fields'], $key, $data_field);
            }
        }
        return $definition;
    }

    protected function generateField(&$definition, $key, $data_field) {
        if ($data_field instanceof stdClass) {
            $definition[$key] = array('type' => 'collection');
            $array_keys_data_field = null;
            $multiple = false;
            foreach ($data_field as $key_sub => $data_field_sub) {
                if ($data_field_sub instanceof stdClass || is_array($data_field_sub)) {
                    if (is_array($array_keys_data_field) && count(array_diff($array_keys_data_field, array_keys((array) $data_field_sub))) < 1) {
                        $multiple = true;
                    }
                    $array_keys_data_field = array_keys((array) $data_field_sub);
                }
            }
            $this->generateDefinition($definition[$key], $data_field, $multiple);
        } elseif (is_array($data_field)) {
            $definition[$key] = array('type' => 'array_collection');
            $this->generateDefinition($definition[$key], $data_field, true);
        } elseif (is_float($data_field)) {
            $definition[$key] = array('type' => 'float');
        } elseif (is_int($data_field)) {
            $definition[$key] = array('type' => 'integer');
        } else {
            $definition[$key] = array();
        }

        return $definition;
    }

}
