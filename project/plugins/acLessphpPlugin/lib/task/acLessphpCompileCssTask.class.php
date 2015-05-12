<?php

class acLessphpCompileCssTask extends sfBaseTask 
{

  protected function configure()
  {
	$this->addArguments(array(
		new sfCommandArgument('input', sfCommandArgument::REQUIRED, 'Less file'),
		new sfCommandArgument('output', sfCommandArgument::REQUIRED, 'CSS file'),
	));
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'aclessphp';
    $this->name             = 'compile';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importConfiguration|INFO] task does things.
Call it with:

  [php symfony aclessphp:compile|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
  	require_once(dirname(__FILE__).'/../vendor/lessphp/lessc.inc.php');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $lessFile = $arguments['input'];
    $cssFile = $arguments['output'];
  	if (file_exists($cssFile)) {
  		unlink($cssFile);
  	}
  	try {
	    lessc::ccompile($lessFile, $cssFile);
	    $this->logSection('compile', 'done', null, 'INFO');
	} catch (Exception $e) {
	    exit('lessc fatal error:<br />'.$e->getMessage());
	}
  }

}
