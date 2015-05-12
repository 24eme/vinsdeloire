<?php

class acVinSV12PluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      $this->dispatcher->connect('routing.load_configuration', array('SV12Routing', 'listenToRoutingLoadConfigurationEvent'));
  }
}
