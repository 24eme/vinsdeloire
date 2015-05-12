<?php

class DRMDetailExportForm extends DRMESDetailsForm {

    public function getFormName() {

        return 'drm_detail_export';
    }

    public function getFormItemClass() {
        
        return 'DRMDetailExportItemForm';
    }

    public function getFormTemplateClass() {

        return 'DRMDetailExportTemplateForm';
    }
    
}