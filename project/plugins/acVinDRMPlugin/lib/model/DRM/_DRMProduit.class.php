<?php
/**
 * Inheritance tree class _DRMProduit
 *
 */

abstract class _DRMProduit extends acCouchdbDocumentTree {

	abstract public function getDeclaration();

	public function getConfig() {
		return $this->getDeclaration()->getConfig();
	}

	/*
	 * Fonction basée sur le flag 
	 */
	public function hasMouvement() {
		foreach($this as $item) {
			if ($item->hasMouvement()) {
				return true;
			}
		}
		return false;
	}
}