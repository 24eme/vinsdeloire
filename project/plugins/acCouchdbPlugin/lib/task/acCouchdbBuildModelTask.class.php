<?php

class acCouchdbBuildModelTask extends sfBaseTask {

    protected function configure() {

        $this->addArguments(array(
            new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'Document model to generate'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', sfConfig::get("sf_lib_dir") . "/model/couchdb"),
        ));

        $this->namespace = 'couchdb';
        $this->name = 'build-model';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [acCouchdbBuildModel|INFO] task does things.
Call it with:

  [php symfony acCouchdbBuildModel|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $model = $arguments['model'];
        
        if (!is_dir($options['dir'])) {
            throw new sfCommandException("dir does not exist");
        }
        
        $dir = $options['dir'].'/'.$model;
        @mkdir($dir);
        $base_dir = $dir.'/base';
        @mkdir($base_dir);
        @mkdir($dir.'/client');
        sfToolkit::clearDirectory($base_dir);

       
        $definition = acCouchdbManager::getDefinition($model);
        $base_filename = "Base" . $model . ".class.php";
        $filename = $model . ".class.php";
        file_put_contents($base_dir . '/' . $base_filename, $this->getFileContentBaseDocument(array("%MODEL_NAME%" => $model,
                    "%PROPERTIES%" => $this->generateProperties($definition),
                    "%EXTENDS%" => ($definition->getInheritance()) ? $definition->getInheritance() : 'acCouchdbDocument',
                    "%METHODS%" => $this->generateMethods($definition),
                )));
        $this->logSection("base document class generated", $base_dir . '/' . $base_filename);

        if (!is_file($dir . '/' . $filename)) {
            file_put_contents($dir . '/' . $filename, $this->getFileContentDocument(array("%MODEL_NAME%" => $model)));
            $this->logSection("class document generated", $dir . '/' . $filename);
        }

        $client_path_filename = $dir . '/client/' . $model."Client.class.php";
        if (!is_file($client_path_filename)) {
            file_put_contents($client_path_filename, $this->getFileContentClient(array("%MODEL_NAME%" => $model)));
            $this->logSection("client class generated", $client_path_filename);
        }

        $this->generateTreeClasses($dir, $base_dir, $definition);
    }
    
    protected function generateTreeClasses($dir, $base_dir, $definition) {
        $fields = $definition->getFields();
        foreach ($fields as $field) {
            if ($field->isCollection() && $field->getCollectionClass() != 'acCouchdbJson') {
                $model = $field->getCollectionClass();
                $base_filename = "Base" . $model . ".class.php";
                $filename = $model . ".class.php";

                file_put_contents($base_dir . '/' . $base_filename, $this->getFileContentBaseTree(array("%MODEL_NAME%" => $definition->getModel(),
                            "%TREE_MODEL_NAME%" => $model,
                            "%EXTENDS%" => ($field->getCollectionInheritance()) ? $field->getCollectionInheritance() : 'acCouchdbDocumentTree',
                            "%PROPERTIES%" => $this->generateProperties($field->getDefinition()),
                            "%METHODS%" => $this->generateMethods($field->getDefinition()),
                        )));
                $this->logSection("base tree class generated", $base_dir . '/' . $base_filename);
                
                /*if ($field->getCollectionInheritance()) {
                    $filename_inheritance = $field->getCollectionInheritance().".class.php";
                    if (!is_file($dir . '/' . $filename_inheritance)) {
                        file_put_contents($dir . '/' . $filename_inheritance, $this->getFileContentTreeInheritance(array("%MODEL_NAME%" => $definition->getModel(),
                            "%INHERITANCE_MODEL_NAME%" => $field->getCollectionInheritance(),
                        )));
                        $this->logSection("inheritance tree class generated", $dir . '/' . $filename_inheritance);
                    } 
                }*/

                if (!is_file($dir . '/' . $filename)) {
                    file_put_contents($dir . '/' . $filename, $this->getFileContentTree(array("%TREE_MODEL_NAME%" => $model)));
                    $this->logSection("tree class generated", $dir . '/' . $filename);
                }
            }
            
            if ($field->isCollection()) {
                $this->generateTreeClasses($dir, $base_dir, $field->getDefinition());
            }
        }
    }

    protected function generateProperties($definition) {
        $properties = "";
        $property_squelette = " * @property %TYPE% \$%NAME%\n";
        $fields = $definition->getFields();
        foreach ($fields as $key => $field) {
            if (!$field->isMultiple()) {
                $values = array("%TYPE%" => $field->getPhpType(),
                    "%NAME%" => $key);
                $properties .= str_replace(array_keys($values), array_values($values), $property_squelette);
            }
        }
        return $properties;
    }

    protected function generateMethods($definition) {
        $methods = "";
        $method_get_squelette = " * @method %TYPE% get%NAME%()\n";
        $method_set_squelette = " * @method %TYPE% set%NAME%()\n";
        $fields = $definition->getFields();
        foreach ($fields as $key => $field) {
            if (!$field->isMultiple()) {
                $values = array("%TYPE%" => $field->getPhpType(),
                    "%NAME%" => sfInflector::camelize($key));
                $methods .= str_replace(array_keys($values), array_values($values), $method_get_squelette);
                $methods .= str_replace(array_keys($values), array_values($values), $method_set_squelette);
            }
        }
        return $methods;
    }

    protected function getFileContentClient($values) {
	return str_replace(array_keys($values), array_values($values), "<?php

class %MODEL_NAME%Client extends acCouchdbClient {
    public static function getInstance()
    {
      return acCouchdbManager::getClient(\"%MODEL_NAME%\");
    }  
}
");
    }

    protected function getFileContentBaseDocument($values) {
        return str_replace(array_keys($values), array_values($values), "<?php
/**
 * Base%MODEL_NAME%
 * 
 * Base model for %MODEL_NAME%
 *
%PROPERTIES%
%METHODS% 
 */
 
abstract class Base%MODEL_NAME% extends %EXTENDS% {

    public function getDocumentDefinitionModel() {
        return '%MODEL_NAME%';
    }
    
}");
    }

    protected function getFileContentDocument($values) {
        return str_replace(array_keys($values), array_values($values), "<?php
/**
 * Model for %MODEL_NAME%
 *
 */

class %MODEL_NAME% extends Base%MODEL_NAME% {

}");
    }
    
    protected function getFileContentBaseTree($values) {
        return str_replace(array_keys($values), array_values($values), "<?php
/**
 * Base%TREE_MODEL_NAME%
 * 
 * Base model for %TREE_MODEL_NAME%

%PROPERTIES%
%METHODS% 
 */

abstract class Base%TREE_MODEL_NAME% extends %EXTENDS% {
                
    public function configureTree() {
       \$this->_root_class_name = '%MODEL_NAME%';
       \$this->_tree_class_name = '%TREE_MODEL_NAME%';
    }
                
}");
    }
    
    protected function getFileContentTree($values) {
        return str_replace(array_keys($values), array_values($values), "<?php
/**
 * Model for %TREE_MODEL_NAME%
 *
 */

class %TREE_MODEL_NAME% extends Base%TREE_MODEL_NAME% {

}");
    }
    
    protected function getFileContentTreeInheritance($values) {
        return str_replace(array_keys($values), array_values($values), "<?php
/**
 * Inheritance tree class %INHERITANCE_MODEL_NAME%
 *
 */

class %INHERITANCE_MODEL_NAME% extends acCouchdbDocumentTree {

}");
    }

}
