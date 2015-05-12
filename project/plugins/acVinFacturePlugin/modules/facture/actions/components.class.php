<?php

class factureComponents extends sfComponents {

  public function executeChooseSociete() {
    if (!$this->form) {
      $this->form = new FactureSocieteChoiceForm('INTERPRO-inter-loire',
              array('identifiant' => $this->identifiant));
    }
  }
  
  public function executeGenerationMasse() { 
    if (!$this->generationForm) {
      $this->generationForm = new FactureGenerationMasseForm(array('region' => $this->region));
    }
  }
    
}
