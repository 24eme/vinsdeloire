<?php

class acCouchdbJsonDefinitionFieldMultiple extends acCouchdbJsonDefinitionField {
    public function __construct($type = self::TYPE_STRING) {
        parent::__construct('*', $type, false);
        $this->is_multiple = true;
    }
}