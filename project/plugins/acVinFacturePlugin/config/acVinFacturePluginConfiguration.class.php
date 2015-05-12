<?php

class acVinFacturePluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array('FactureRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
