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
class acVinCompteProxy extends BaseacVinCompteProxy 
{
    protected $_compte_reference = null;
    
    /**
     *
     * @return _Compte
     */
    public function getCompteReferenceObject() 
    {
        if (is_null($this->_compte_reference)) {
            $this->_compte_reference = acCouchdbManager::getClient()->retrieveDocumentById($this->compte_reference);
            if (!$this->_compte_reference) {
                throw new sfException("Le compte référence n'existe pas");
            }
        }
        return $this->_compte_reference;
    }
}