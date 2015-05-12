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
 * acVinComptePlugin model.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCompteTiers extends BaseacVinCompteTiers 
{
    protected $_tiers = null;

    /**
     *
     * @return array 
     */
    public function getTiersObject() 
    {
        if (is_null($this->_tiers)) {
	  		$this->_tiers = array();
	  		foreach ($this->tiers as $tiers) {
	    		$this->_tiers[] = acCouchdbManager::getClient()->retrieveDocumentById($tiers->id);
	  		}
        }
        return $this->_tiers;
    }

    public function addTiers($tiers) {
        $tiers_compte = $this->tiers->add($tiers->get('_id'));
        $tiers_compte->id = $tiers->get('_id');
        $tiers_compte->type = $tiers->type;
        $tiers_compte->nom = $tiers->nom;
	return $tiers_compte;
    }

}
