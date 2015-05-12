<?php

class FactureSocieteChoiceForm extends SocieteChoiceForm {

    public function configure()
    {
        parent::configure();
        $this->configureTypeSociete(array(SocieteClient::SUB_TYPE_VITICULTEUR, SocieteClient::SUB_TYPE_NEGOCIANT));
    }

}
