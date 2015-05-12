<?php

abstract class acCouchdbDocument extends acCouchdbDocumentStorable {

    const BIG_DOCUMENT_SIZE = 50000;

    protected $_is_new = true;
    protected $_serialize_loaded_json = null;

    public function loadFromCouchdb(stdClass $data) {
        if (!is_null($this->_serialize_loaded_json)) {
            throw new acCouchdbException("data already load");
        }
    	if (isset($data->_attachments) && !$this->getDefinition()->exist('_attachments')) {
    	  unset($data->_attachments);
        }

        $this->_serialize_loaded_json = serialize(new acCouchdbJsonNative($data));

        $this->load($data);
    }

    public function reloadFromCouchdb(stdClass $data) {
        $this->_serialize_loaded_json = null;
        $this->loadFromCouchdb($data);
    }
    
    public function __toString() {
        return $this->get('_id') . '/' . $this->get('_rev');
    }

    public function __construct() {
        parent::__construct(acCouchdbManager::getDefinitionByHash($this->getDocumentDefinitionModel(), '/'), $this, "");
        $this->type = $this->getDefinition()->getModel();

        if (!$this->type) {
            throw new acCouchdbException('Model should include Type field in the document root');
        }
    }

    public function isNew() {
        if (!$this->_exist('_rev'))
            return true;
        return is_null($this->get('_rev'));
    }

    public function save() {
        if ($this->isNew() && !$this->get('_id')) {
            $this->constructId();
        }

        $this->preSave();
        
        $this->definitionValidation();
        if ($this->isModified()) {
            $this->doSave();
            
            return $this->storeDoc();
        }
        return false;
    }

    public function storeDoc() {
        $ret = acCouchdbManager::getClient()->save($this);
        $this->_rev = $ret->rev;
        $this->_serialize_loaded_json = serialize(new acCouchdbJsonNative($this->getData()));
        
        return $ret;
    }
    
    public function rollBackAndPreserve($revision = null) {
        $doc_to_roll_back = acCouchdbManager::getClient()->getPreviousDoc($this->_id, $revision);
        $doc_to_roll_back->_rev = $this->_rev;
        $ret = acCouchdbManager::getClient()->storeDoc($doc_to_roll_back);
        $this->_rev = $ret->rev;
        return $ret;
    }

    protected function constructId() {
        
    }

    protected function preSave() {
        
    }
    
    protected function doSave() {
        
    }

    protected function postSave($doc) {
        $this->_rev = $doc->rev;
        $this->_serialize_loaded_json = serialize(new acCouchdbJsonNative($this->getData()));
    }
    
    public function getData() {
        $data = parent::getData();
        if ($this->isNew()) {
            unset($data->_rev);
        }
        return $data;
    }

    public function getDocumentDefinitionModel() {
        throw new acCouchdbException('Definition model not implemented');
    }

    public function delete() {
        $ret = acCouchdbManager::getClient()->delete($this);

        $this->_rev = null;

        return $ret;
    }

    public function storeAttachment($file, $content_type = 'application/octet-stream', $filename = null) { 
        $ret = acCouchdbManager::getClient()->storeAttachment($this, $file, $content_type, $filename);
	if (isset($ret->error))
	  throw new sfException($ret->reason);
        $this->postSave($ret);
        $json = acCouchdbManager::getClient()->find($this->_id, acCouchdbClient::HYDRATE_JSON);
        
        $this->_attachments = $json->_attachments;

        return $ret;
    }
    

    public function getAttachmentUri($filename) {

        return 'http://localhost:5984'.acCouchdbManager::getClient()->getAttachmentUri($this, $filename);
    }

    public function loadAllData() {

        return parent::loadAllData();
    }

    public function update($params = array()) {

        return parent::update($params);
    }

    public function init($params = array()) {

        return parent::init($params);
    }

    public function getModifications() {
        $native_json = unserialize($this->_serialize_loaded_json);
        $final_json = new acCouchdbJsonNative($this->getData());

        return $final_json->diff($native_json);
    }

    public function isModified() {
        if(strlen($this->_serialize_loaded_json) > self::BIG_DOCUMENT_SIZE) {

            return true;
        };

        $native_json = unserialize($this->_serialize_loaded_json);
        $final_json = new acCouchdbJsonNative($this->getData());
        return $this->isNew() || (!$native_json->equal($final_json));
    }

    protected function reset($document) {
        parent::reset($this);
        $this->_is_new = true;
        $this->_serialize_loaded_json = null;
    }

    public function __clone() {
        $data = $this->getData();
        $this->reset($this);
        $this->loadFromCouchdb($data);
        $this->_rev = null;
        $this->_id = null;
    }
}
