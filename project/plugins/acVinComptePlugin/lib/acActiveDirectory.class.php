<?php

class acActiveDirectory {

  function __construct($param = array()) {
    if (isset($param['defaultuser'])) {
      $this->defaultuser = $param['defaultuser'];
    }
    if (isset($param['user'])) {
      $this->defaultuser = $param['user'];
    }
    if (!isset($this->defaultuser) || !$this->defaultuser) {
      $this->defaultuser = sfConfig::get('app_ad_defaultuser');
    }
    if (isset($param['defaultpassword'])) {
      $this->defaultpassword = $param['defaultpassword'];
    }
    if (isset($param['password'])) {
      $this->defaultpassword = $param['password'];
    }
    if (!isset($this->defaultpassword) || !$this->defaultpassword) {
      $this->defaultpassword = sfConfig::get('app_ad_defaultpassword');
    }
    if (isset($param['domain'])) {
      $this->domain = $param['domain'];
    }
    if (!isset($this->domain) || !$this->domain) {
      $this->domain = sfConfig::get('app_ad_domain');
    }
    if (isset($param['host'])) {
      $this->host = $param['host'];
    }
    if (!isset($this->host) || !$this->host) {
      $this->host = sfConfig::get('app_ad_host');
    }
    if (isset($param['port'])) {
      $this->port = $param['port'];
    }
    if (!isset($this->port) || !$this->port) {
      $this->port = sfConfig::get('app_ad_port', 389);
    }
    if (isset($param['basedn'])) {
      $this->basedn = $param['basedn'];
    }
    if (!isset($this->basedn) || !$this->basedn) {
      $this->basedn = sfConfig::get('app_ad_basedn');
    }
  }

  function connect($param = array()) {
    if (!isset($this->host) || !isset($this->basedn)) {
      throw new sfException('host or basdn missing');
    }
    if (!isset($param['user'])) {
      $param['user'] = $this->defaultuser;
    }
    if (!isset($param['password'])) {
      $param['password'] = $this->defaultpassword;
    }
    if (!isset($param['domain'])) {
      $param['domain'] = $this->domain;
    }
    if (!preg_match('/[\\\]/', $param['user'])) {
      $param['user'] = $param['domain'].'\\'.$param['user'];
    }

    if (! ($this->ad = ldap_connect('ldap://'.$this->host.':'.$this->port))) {
      throw new sfException('Could not connect to LDAP server : '.'ldap://'.$this->host.':'.$this->port);
    }
    ldap_set_option($this->ad, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($this->ad, LDAP_OPT_REFERRALS, 0);
    if (! @ldap_bind($this->ad, $param['user'], $param['password'])) {
      throw new sfException('Could not bind to AD : '.$param['user'].'@'.$param['password']);
    }
  }

  function getDescription($samaccountname) {
    if (!isset($this->ad) || !$this->ad) {
      $this->connect();
   }
    $attributes = array('description');
    $result = ldap_search($this->ad, $this->basedn, "(samaccountname={$samaccountname})", $attributes);
    if (!$result) {
      return '';
    }
    $entries = ldap_get_entries($this->ad, $result);
    if ($entries['count']>0 && isset($entries[0]['description'])) {
      return $entries[0]['description'][0];
    }
    return '';
  }

}