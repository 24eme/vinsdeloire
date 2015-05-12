<?php
/**
 * Model for ConfigurationDetail
 *
 */

class ConfigurationDetail extends BaseConfigurationDetail {

  public function getCVO($periode, $interpro) {
    return $this->getParent()->getParent()->getCVO($periode, $interpro);
  }
	
}