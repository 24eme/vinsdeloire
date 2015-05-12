<?php
/**
 * Model for DRMCertification
 *
 */

class DRMCertification extends BaseDRMCertification {

	public function getPreviousSisterWithMouvementCheck() {
		
		return null;
	}

	public function getNextSisterWithMouvementCheck() {

		return null;
	}

	public function getChildrenNode() {

		return $this->genres;
	}

}