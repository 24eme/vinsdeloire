<?php

class acVinDocumentPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      $this->dispatcher->connect('routing.load_configuration', array('DocumentRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
