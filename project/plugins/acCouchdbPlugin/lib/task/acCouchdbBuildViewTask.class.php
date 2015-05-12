<?php

class acCouchdbBuildViewTask extends sfBaseTask {

    protected function configure() {

        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'couchdb';
        $this->name = 'build-view';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [acCouchdbLoadView|INFO] task does things.
Call it with:

  [php symfony acCouchdbLoadView|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
    	$databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    	$file_regexp = "/([a-zA-Z0-9\-\_]+)\.([a-zA-Z0-9\-\_]+)\.(map|reduce)\.view\.js$/";

    	$files = sfFinder::type("file")->name($file_regexp)->in(sfConfig::get('sf_root_dir'));

    	$designs = array();

    	foreach($files as $file) {
    		preg_match($file_regexp, $file, $matches);
    		$name = $matches[1];
    		$design = isset($designs[$name]) ? $designs[$name] : $this->getDesign($name);
    		$content = file_get_contents($file);
    		if ($content) {
    			@$design->views->{$matches[2]}->{$matches[3]} = $content;
    		}
    		$designs[$name] = $design;
    	}

    	foreach($designs as $design) {
    		acCouchdbManager::getClient()->storeDoc($design);
    		foreach($design->views as $view => $content) {
    			echo acCouchdbManager::getClient()->getDatabaseUri().'/'.$design->_id."/_view/".$view."\n";
    		}
    	}
    }

    protected function getDesign($name) {
    	$id = '_design/'.$name;
    	
    	$design = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
    	if (!$design) {
    		$design = new stdClass();
    		$design->_id = '_design/'.$name;
    		$design->language = 'javascript';
    	}

    	$design->views = new stdClass();

    	return $design;
    }
}
