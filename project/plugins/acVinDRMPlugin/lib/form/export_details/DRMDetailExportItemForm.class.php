<?php

class DRMDetailExportItemForm extends DRMESDetailsItemForm {

    public function getFormName() {

        return "drm_detail_export_item";
    }

    public function getIdentifiantChoices() {

        return ConfigurationClient::getInstance()->getCountryList();
    }

    public function getPostValidatorClass() {

        return 'DRMDetailExportItemValidator';
    }

}