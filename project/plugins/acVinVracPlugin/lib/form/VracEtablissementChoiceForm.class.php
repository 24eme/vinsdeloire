<?php

class VracEtablissementChoiceForm extends EtablissementChoiceForm {

    public function configure()
    {
        parent::configure();
        $this->getWidget('identifiant')->setLabel("Rechercher un opérateur : ");
    }

}