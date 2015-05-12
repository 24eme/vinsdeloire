<?php

class SV12EtablissementChoiceForm extends EtablissementChoiceForm {
        
    public function configure()
    {
        parent::configure();
        $this->configureFamilles(EtablissementFamilles::FAMILLE_NEGOCIANT);
    }
  
}