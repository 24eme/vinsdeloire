<?php
/**
 * Model for DRMDetailDroit
 *
 */

class DRMDetailDroit extends BaseDRMDetailDroit {
    protected function getConfig($interpro = 'INTERPRO-inter-loire') {

        return $this->getParent()->getCepage()->getConfig()->getDroits($interpro)->get($this->getKey())->getCurrentDroit($this->getDocument()->getDate());
    }

    protected function init($params = array()) {
        parent::init($params);

        $this->taux = null;
    }

    public function calcul() {
        if (is_null($this->taux)) {
            $this->taux = $this->getConfig()->taux;
        }
    }
}