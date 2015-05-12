<?php

class DRMEtablissementChoiceForm extends EtablissementChoiceForm {

    public function configure()
    {
        parent::configure();
        $this->configureFamilles(EtablissementFamilles::FAMILLE_PRODUCTEUR);
    }

}

