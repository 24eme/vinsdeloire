<?php

class acVinDRMPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      $this->dispatcher->connect('routing.load_configuration', array('DRMRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
