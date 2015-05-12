<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acVinComptePlugin task.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCompteCreateVirtuelTask extends sfBaseTask
{
  protected function configure()
  {
     $this->addArguments(array(
        new sfCommandArgument('login', sfCommandArgument::REQUIRED, 'Login'),
        new sfCommandArgument('pass', sfCommandArgument::REQUIRED, 'Mot de passe'),
        new sfCommandArgument('nom', sfCommandArgument::REQUIRED, 'Nom'),
        new sfCommandArgument('email', sfCommandArgument::REQUIRED, 'Email'),
        new sfCommandArgument('commune', sfCommandArgument::REQUIRED, 'Commune'),
        new sfCommandArgument('code_postal', sfCommandArgument::REQUIRED, 'Code Postal'),
        new sfCommandArgument('droits', sfCommandArgument::IS_ARRAY, 'Droits'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declarvin'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'compte';
    $this->name             = 'create-virtuel';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiersCreateFictif|INFO] task does things.
Call it with:

  [php symfony tiersCreateFictif|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if (acCouchdbManager::getClient()->retrieveDocumentById('COMPTE-'.$arguments['login'])) {
        throw new sfCommandException(sprintf("Le compte \"%s\" existe déjà", $arguments['login']));
    }
    
    $compte = new CompteVirtuel();
    $compte->set('_id', 'COMPTE-'.$arguments['login']);
    $compte->login = $arguments['login'];
    $compte->setPasswordSSHA($arguments['pass']);
    $compte->email = $arguments['email'];
    $compte->nom = $arguments['nom'];
    $compte->commune = $arguments['commune'];
    $compte->code_postal = $arguments['code_postal'];
    $compte->droits = $arguments['droits'];
    $compte->save();
    
    $this->logSection("created", $compte->login);
  }
}