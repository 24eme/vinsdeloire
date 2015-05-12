<?php

class acCouchdbViewsTask extends sfBaseTask {

    protected function configure() {

        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'couchdb';
        $this->name = 'views';
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

    	$files = sfFinder::type("file")->name($file_regexp)->relative()->in(sfConfig::get('sf_root_dir'));

    	$views = array();

    	foreach($files as $file) {
    		preg_match($file_regexp, $file, $matches);
    		$name = '_design/'.$matches[1]."/_view/".$matches[2];
    		$views[$name] = $file;
    	}

    	ksort($views);

    	foreach($views as $view => $path) {
    		
    		$this->logSection($view, $path);
    	}
    }
}