<?php

class DSEtablissementChoiceForm extends EtablissementChoiceForm {

    public function configure()
    {
        parent::configure();
        $this->configureFamilles(array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_NEGOCIANT));
    }

}
