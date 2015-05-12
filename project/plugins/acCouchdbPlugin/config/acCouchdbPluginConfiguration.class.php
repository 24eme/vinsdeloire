<?php

class acCouchDbPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      sfConfig::set('sf_orm', 'couchdb');
      if ($this->configuration instanceof sfApplicationConfiguration) {
        acCouchdbManager::setSchema(include $this->configuration->getConfigCache()->checkConfig('config/couchdb/schema.yml'));
      }
  }
}
