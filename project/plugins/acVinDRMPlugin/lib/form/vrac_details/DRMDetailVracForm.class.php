<?php

class DRMDetailVracForm extends DRMESDetailsForm {

    public function getFormName() {

        return 'drm_detail_vrac';
    }

    public function getFormItemClass() {
        
        return 'DRMDetailVracItemForm';
    }

    public function getFormTemplateClass() {

        return 'DRMDetailVracTemplateForm';
    }
    
}