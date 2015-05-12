<?php

class acVinDSPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array('DSRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
