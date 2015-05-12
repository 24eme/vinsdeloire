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
 * acVinComptePlugin configuration.
 * 
 * @package    acVinComptePlugin
 * @subpackage plugin
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
abstract class acVinCompteSecurityUser extends sfBasicSecurityUser 
{

    protected $_compte = null;
    const SESSION_COMPTE = 'compte';
    const NAMESPACE_COMPTE_TIERS = "CompteSecurityUser_Tiers";
    const NAMESPACE_COMPTE_PROXY = "CompteSecurityUser_Proxy";
    const NAMESPACE_COMPTE_VIRTUEL = "CompteSecurityUser_Virtuel";
    const CREDENTIAL_COMPTE = 'compte';
    const CREDENTIAL_COMPTE_TIERS = 'compte_tiers';
    const CREDENTIAL_COMPTE_PROXY = 'compte_proxy';
    const CREDENTIAL_COMPTE_VIRTUEL = 'compte_virtuel';
    const CREDENTIAL_ADMIN = 'admin';
    const CREDENTIAL_OPERATEUR = 'operateur';

    protected $_couchdb_type_namespace_compte= array("CompteTiers" => self::NAMESPACE_COMPTE_TIERS, 
                                                     "CompteProxy" => self::NAMESPACE_COMPTE_PROXY, 
                                                     "CompteVirtuel" => self::NAMESPACE_COMPTE_VIRTUEL);
    
    protected $_namespace_credential_compte = array(self::NAMESPACE_COMPTE_TIERS => self::CREDENTIAL_COMPTE_TIERS, 
                                                   self::NAMESPACE_COMPTE_PROXY => self::CREDENTIAL_COMPTE_PROXY,
                                                   self::NAMESPACE_COMPTE_VIRTUEL => self::CREDENTIAL_COMPTE_VIRTUEL);
    
    protected $_namespaces_compte = array(self::NAMESPACE_COMPTE_TIERS, 
                                          self::NAMESPACE_COMPTE_PROXY, 
                                          self::NAMESPACE_COMPTE_VIRTUEL);
    
    protected $_credentials_compte = array(self::CREDENTIAL_COMPTE, 
                                           self::CREDENTIAL_COMPTE_TIERS, 
                                           self::CREDENTIAL_COMPTE_PROXY, 
                                           self::CREDENTIAL_COMPTE_VIRTUEL,
                                           self::CREDENTIAL_ADMIN,
                                           self::CREDENTIAL_OPERATEUR);

    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options 
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) 
    {
        parent::initialize($dispatcher, $storage, $options);
        if (!$this->isAuthenticated()) {
            $this->signOut();
        }
    }

    /**
     *
     * @param string $cas_user 
     */
    public function signIn($cas_user) 
    {
        var_dump('here');
        $compte = acCouchdbManager::getClient('_Compte')->retrieveByLogin($cas_user);
        if (!$compte) {
            throw new sfException('compte does not exist');
        }
        $this->addCredential(self::CREDENTIAL_COMPTE);
        $this->signInCompte($compte);
        $this->setAuthenticated(true);
    }
    
    /**
     *
     * @param _Compte $compte 
     */
    public function signInFirst($compte) 
    {
        $this->addCredential(self::CREDENTIAL_COMPTE);
        $this->signInCompte($compte);
        $this->setAuthenticated(true);
    }

    /**
     * 
     */
    public function signOut() 
    {
        foreach($this->_namespaces_compte as $namespace) {
            $this->signOutCompte($namespace);
        }
        $this->setAuthenticated(false);
        $this->clearCredentials();
    }

    /**
     *
     * @param _Compte $compte 
     */
    public function signInCompte($compte) 
    {
        $namespace = $this->_couchdb_type_namespace_compte[$compte->type];
        $this->signOutCompte($namespace);
        $this->setAttribute(self::SESSION_COMPTE, $compte->login, $namespace);
        $this->addCredential($this->_namespace_credential_compte[$namespace]);
        foreach ($compte->droits as $credential) {
                $this->addCredential($credential);
        }
        if ($this->hasCredential(self::CREDENTIAL_ADMIN)) {
            $this->addCredential(self::CREDENTIAL_OPERATEUR);
        }
        if ($compte->type == 'CompteProxy') {
            $this->signInCompte(acCouchdbManager::getClient()->retrieveDocumentById($compte->compte_reference));
        }
    }
    
    /**
     *
     * @param string $namespace 
     */
    public function signOutCompte($namespace) 
    {
        $this->_compte = null;
        $this->removeCredential($this->_namespace_credential_compte[$namespace]);
        $this->getAttributeHolder()->removeNamespace($namespace);
    }

    /**
     *
     * @return _Compte $compte 
     */
    public function getCompte() 
    {
        $this->requireCompte();
        if (is_null($this->_compte)) {
            $this->_compte = acCouchdbManager::getClient('_Compte')->retrieveByLogin($this->getAttribute(self::SESSION_COMPTE, null, $this->getNamespaceCompte()));
            if (!$this->_compte) {
                $this->signOut();
                throw new sfException("The compte does not exist");
            }
        }
        return $this->_compte;
    }
    
    protected function getNamespaceCompte() 
    {
        if($this->hasCredential(self::CREDENTIAL_COMPTE_PROXY)) {
            return self::NAMESPACE_COMPTE_PROXY;
        } elseif ($this->hasCredential(self::CREDENTIAL_COMPTE_TIERS)) {
            return self::NAMESPACE_COMPTE_TIERS;
        } elseif($this->hasCredential(self::CREDENTIAL_COMPTE_VIRTUEL)) {
            return self::NAMESPACE_COMPTE_VIRTUEL;
        } else {
            throw new sfException("no namespace existing");
        }
    }

    /**
     * 
     */
    protected function requireCompte() 
    {
        if (!$this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_COMPTE)) {
	  		throw new sfException("you must be logged with a tiers");
        }
    }
    
        public function getDeclarantVrac() {
        $declarants = $this->getDeclarantsVrac();
		
        return current($declarants);
    }

    public function getDeclarantsVrac() {
        $declarants = array();
        $tiers = $this->getTiers();

        if($tiers->type == 'Recoltant' && isset($this->_tiers['MetteurEnMarche']) && $this->_tiers['MetteurEnMarche']->qualite_categorie == 'Negociant') {

            $declarants[$this->_tiers['MetteurEnMarche']->_id] = $this->_tiers['MetteurEnMarche'];
        }

        $declarants[$tiers->_id] = $tiers;
        return $declarants;
    }

}