<?php

abstract class BaseLS extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'LS';
    }
}