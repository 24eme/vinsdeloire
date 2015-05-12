<?php
/**
 * Model for ConfigurationDroits
 *
 */

class ConfigurationDroits extends BaseConfigurationDroits {
	
	const CODE_CVO = 'CVO';
	const LIBELLE_CVO = 'Cvo';

	const DROIT_CVO = 'cvo';
	const DROIT_DOUANE = 'douane';
	
	public function addDroit($date, $taux, $code, $libelle) {
	  $value = $this->add();
	  $value->date = $date;
	  $value->taux = $taux;
	  $value->code = $code;
	  $value->libelle = $libelle;
	}
	
	public function getCurrentDroit($date_cvo) {
	  $currentDroit = null;
	  foreach ($this as $configurationDroit) {
	    $date = new DateTime($configurationDroit->date);
	    if ($date_cvo >= $date->format('Y-m-d')) {
	      if ($currentDroit) {
		if ($date->format('Y-m-d') > $currentDroit->date) {
		  $currentDroit = $configurationDroit;
                }
	      } else {
		$currentDroit = $configurationDroit;
	      }
	    }
	  }

	  if ($currentDroit) {
	    return $currentDroit;
	  }

	  try {
	    $parent = $this->getNoeud()->getParentNode();
	    return $parent->interpro->getOrAdd($this->getInterpro()->getKey())->droits->getOrAdd($this->getKey())->getCurrentDroit($date_cvo);
	  } catch (sfException $e) {
	    throw new sfException('Aucun droit spécifié pour '.$this->getHash());
	  }
	}
	
	public function getInterpro() {
		return $this->getParent()->getParent();
	}

	public function getNoeud() {

		return $this->getInterpro()->getParent()->getParent();
	}

}