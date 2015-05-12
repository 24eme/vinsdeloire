<?php

class DRMDetailCooperativeForm extends DRMESDetailsForm {

    public function getFormName() {

        return 'drm_detail_cooperative';
    }

    public function getFormItemClass() {
        
        return 'DRMDetailCooperativeItemForm';
    }

    public function getFormTemplateClass() {

        return 'DRMDetailCooperativeTemplateForm';
    }
    
}