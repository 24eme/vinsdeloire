<?php

class acCouchdbJsonDefinitionFieldMultipleCollection extends acCouchdbJsonDefinitionFieldCollection {
    public function __construct($model, $hash, $collection_class = 'acCouchdbJson', $inheritance = null) {
        $definition = parent::__construct('*', false, $model, $hash, $collection_class, $inheritance);
        $this->is_multiple = true;
        return $definition;
    }
}