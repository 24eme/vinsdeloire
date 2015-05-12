<?php

/* This file is part of the acCouchdbPlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acCouchdbDatabase model.
 * 
 * @package    acCouchdbPlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */


class acElasticaDatabase extends sfDatabase
{
    
  /**
   * Initialize couchdb client
   * 
   * @param array $parameters parameters of the connection to database
   * @return void
   */  
  public function initialize($parameters = array())
  {
    parent::initialize($parameters);
    acElasticaManager::initializeClient($this->getParameter('dsn'), $this->getParameter('dbname'));
  }

  /**
   * Initializes the connection and sets it to object.
   *
   * @return void
   */
  public function connect()
  {
    $this->connection = acElasticaManager::getIndex();
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      $this->connection = null;
    }
  }
}