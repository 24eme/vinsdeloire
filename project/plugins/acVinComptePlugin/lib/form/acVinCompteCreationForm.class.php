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
 * acVinComptePlugin form.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCreationCompteForm extends acVinCompteForm 
{    
    /**
     * 
     */
    protected function checkCompte() 
    {
        parent::checkCompte();
        if ($this->_compte->getStatut() != _Compte::STATUT_NOUVEAU) {
            throw new sfException("compte must be status : NOUVEAU");
        }
    }
    
    /**
     * 
     */
    public function configure() 
    {
        parent::configure();
    }
    
    /**
     *
     * @return _Compte 
     */
    public function save() 
    {
        if ($this->isValid()) {
            $this->_compte->email = $this->getValue('email');
            $this->_compte->setPasswordSSHA($this->getValue('mdp1'));
            $this->_compte->save();
            $this->_compte->updateLdap();
        } else {
            throw new sfException("form must be valid");
        }
        
        return $this->_compte;
    }
}