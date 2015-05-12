<?php

class vracComponents extends sfComponents {

    public function executeFormEtablissementChoice() {
        if (!$this->identifiant) {
            $this->identifiant = null;
        }

        if (!$this->form) {
            $this->form = new VracEtablissementChoiceForm('INTERPRO-inter-loire', array('identifiant' => $this->identifiant));
        }
    }
}