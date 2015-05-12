<?php

class acVinAnnuairePluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
  	$this->dispatcher->connect('routing.load_configuration', array('AnnuaireRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
