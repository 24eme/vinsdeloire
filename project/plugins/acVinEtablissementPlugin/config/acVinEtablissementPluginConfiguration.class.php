<?php

class acVinEtablissementPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      $this->dispatcher->connect('routing.load_configuration', array('acVinEtablissementRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
