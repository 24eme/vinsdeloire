<?php

class Lock extends BaseLock {

  protected static function getInstance() {
    $lock = acCouchdbManager::getClient()->find('Lock');
    if ($lock) {
      return $lock;
    }
    $lock = new Lock();
    $lock->_id = 'Lock';
    $lock->type = 'Lock';
    return $lock;
  }

  public static function runLock($ilockobject, $args =  null) {
    for($i = 0 ; $i < 10 ; $i++) {
      try {
        $lock = Lock::getInstance();
        //echo sprintf("RECUPERATION DU LOCK;%s;%s;%s\n", date('Y-m-d H:i:s'), $ilockobject->getDocument()->get('_id'), $lock->{$ilockobject->getDocument()->get('type')});
        $res = $ilockobject->executeLock($args);
        if (!isset($res['key'])) {

          throw new sfException('executeLock needs to return a hashtable with an element "key"');
        }
        if (!isset($res['value'])) {

          throw new sfException('executeLock needs to return a hashtable with an element "value"');
        }
        if($lock->exist($res['key']) && $lock->get($res['key']) == $res['value']) {
          $docid = preg_replace('/^.*:/', '', $lock->get($res['value']));
          if(acCouchdbManager::getClient()->find($docid, acCouchdbClient::HYDRATE_JSON)) {
            throw new sfException('the archive value need to be different from the lock one');
          }
        }
      }catch(Exception $e) {
        continue;
      }
        $lock->add($res['key'], $res['value'].':'.$res['docid']);
        $rev = ($ilockobject->getDocument()->_rev) ? $ilockobject->getDocument()->_rev : null;
        $ilockobject->getDocument()->storeDoc();
        $doc = acCouchdbManager::getClient()->find($ilockobject->getDocument()->_id, acCouchdbClient::HYDRATE_JSON);
      try {
        $lock->save();
        //echo sprintf("ENREGISTREMENT DU LOCK;%s;%s;%s\n", date('Y-m-d H:i:s'), $res['docid'], $res['value']);
        break;
      }catch(Exception $e) {
        $lock = acCouchdbManager::getClient()->find('Lock', acCouchdbClient::HYDRATE_JSON);
        $doc->numero_archive = null;
        $ret = acCouchdbManager::getClient()->storeDoc($doc);
        $ilockobject->getDocument()->_rev = $ret->rev;
        try {
          acCouchdbManager::getClient()->storeDoc($lock);
        }catch(Exception $e) {
          try {
            $lock = acCouchdbManager::getClient()->find('Lock', acCouchdbClient::HYDRATE_JSON);
            acCouchdbManager::getClient()->storeDoc($lock);
          } catch(Exception $e) {

          }
        }
        if($rev) {
          $ilockobject->getDocument()->rollBackAndPreserve($rev);
        } else {
          $ilockobject->getDocument()->delete();
        }
        //echo sprintf("ROLLBACK DU DOCUMENT;%s;%s;%s\n", date('Y-m-d H:i:s'), $res['docid'], $ilockobject->getDocument()->_rev);
      }
      sleep(1);
    }
    if ($i >= 10) {
      $ilockobject->getDocument()->numero_archive = null;
      throw new sfException("Could not acquire the archive lock, il faut s'assurer que le numéro d'archive de lock est égal au plus élévé de celui des vues");
    }
  }
}