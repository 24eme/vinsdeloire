<?php

class dsComponents extends sfComponents {

  public function executeChooseEtablissement() {
    if (!$this->form) {
      $this->form = new DSEtablissementChoiceForm('INTERPRO-inter-loire',
              array('identifiant' => $this->identifiant));
    }
  }
    
}