<?php

abstract class BaseLock extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Lock';
    }
    
}