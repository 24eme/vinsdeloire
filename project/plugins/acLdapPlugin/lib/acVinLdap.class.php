<?php

/* This file is part of the acLdapPlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acLdapPlugin lib.
 * 
 * @package    acLdapPlugin
 * @subpackage lib
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @version    0.1
 */
class acVinLdap
{
    protected $serveur;
    protected $dn;
    protected $dc;
    protected $pass;

    /**
     * 
     */
    public function __construct()
    {
		$this->serveur = sfConfig::get('app_ldap_serveur');
		$this->dn = sfConfig::get('app_ldap_dn');
		$this->dc = sfConfig::get('app_ldap_dc');
		$this->pass = sfConfig::get('app_ldap_pass');
    }

    /**
     *
     * @return bool 
     */
    public function connect() 
    {
        $con = ldap_connect($this->serveur);
        ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
        if ($con && ldap_bind($con, $this->dn, $this->pass)) {
            return $con;
        } else {
            return false;
        }
    }
    
    /**
     *
     * @param mixed $uid
     * @param array $infos
     * @return bool 
     */
    public function save($uid, $infos)
    {
      $con = $this->connect();
      if($con) {
	if ($this->exist($uid)) {
	  return $this->update($uid, $infos);
	} else {
	  return $this->add($uid, $infos);
	}
      }
      return false;
    }
    
    /**
     *
     * @param mixed $uid
     * @param array $infos
     * @return bool 
     */
    protected function add($uid, $infos) 
    {
        $con = $this->connect();
        if($con) {
            $add = @ldap_add($con, 'uid='.$uid.',ou=People,'.$this->dc, $infos);
	    if (!$add) {
	      throw new sfException(ldap_error($con));
	    }
            ldap_unbind($con);
            return $add;
        }
        return false;
    }
    
    /**
     *
     * @param mixed $uid
     * @param array $infos
     * @return bool 
     */
    protected function update($uid, $infos)
    {
        $con = $this->connect();
        if($con) {
            $update = @ldap_modify($con, 'uid='.$uid.',ou=People,'.$this->dc, $infos);
	    if (!$update) {
	      throw new sfException(ldap_error($con));
	    }
            ldap_unbind($con);
            return $update;
        }
        return false;
    }

    /**
     *
     * @param mixed $uid
     * @return bool 
     */
    public function exist($uid)
    {
        $con = $this->connect();
        if($con) {
            $search = ldap_search($con, 'ou=People,'.$this->dc, 'uid='.$uid);
            if($search){
                $count = ldap_count_entries($con, $search);
                if($count > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *
     * @param mixed $uid
     * @return bool 
     */
    public function delete($uid) 
    {
        $con = $this->connect();
        if($con) {
            $delete = ldap_delete($con, 'uid='.$uid.',ou=People,'.$this->dc);
            ldap_unbind($con);
            return $delete;
        } else {
            return false;
        }
    }
}