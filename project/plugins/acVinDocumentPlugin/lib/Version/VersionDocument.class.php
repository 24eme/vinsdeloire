<?php

class VersionDocument
{
    protected $document;
    protected $mother = null;
    protected $diff_with_mother = null;
    protected $master = null;

    public function __construct(acCouchdbDocument $document) {
        $this->document = $document;
    }

    public static function buildVersion($rectificative, $modificative) {
        if ($rectificative && $modificative) {
        
            return sprintf('R%02dM%02d', $rectificative, $modificative);
        }

        if($rectificative) {

            return sprintf('R%02d', $rectificative);
        }

        if($modificative) {

            return sprintf('M%02d', $modificative);
        }

        return null;
    }

    public static function buildRectificative($version) {
        if (preg_match('/^R([0-9]{2})/', $version, $matches)) {

            return (int) $matches[1];
        }

        return null;
    }

    public static function buildModificative($version) {
        if (preg_match('/M([0-9]{2})$/', $version, $matches)) {

            return (int) $matches[1];
        }

        return null;
    }

    protected function buildVersionDocument($rectificative, $modificative) {
        $class = get_class($this->document);

        return $class::buildVersion($rectificative, $modificative);
    }

    public function getRectificative() {
        $class = get_class($this->document);
        
        return $class::buildRectificative($this->document->version);
    }

    public function hasVersion() {

        return $this->document->isRectificative() || $this->document->isModificative();
    }

    public function isRectificative() {

        return $this->document->getRectificative() > 0;
    }

    public function getModificative() {
        $class = get_class($this->document);
        
        return $class::buildModificative($this->document->version);
    }

    public function isModificative() {

        return $this->getModificative() > 0;
    }

    public function getPreviousVersion() {
        if($this->isModificative()) {
            
            return $this->buildVersionDocument($this->getRectificative(), $this->getModificative() - 1);
        }

        if($this->isRectificative()) {

            return $this->buildVersionDocument($this->getRectificative() - 1, $this->getModificative());
        }

        return null;
    }

    public function motherGet($hash) {

        return $this->document->getMother()->get($hash);
    }

    public function motherExist($hash) {

        return $this->document->getMother()->exist($hash);
    }

    public function getMother() {
        if (!$this->document->hasVersion()) {
            return null;
        }

        if(is_null($this->mother)) {
            $this->mother = $this->document->findDocumentByVersion($this->document->getPreviousVersion());
        }

        return $this->mother;    
    }

    public function getDiffWithMother() {
        if (is_null($this->diff_with_mother)) {
            $mother = $this->getMother();
            if (!$this->getMother()) {
                $mother = new DRM();
            }
            $this->diff_with_mother = $this->getDiffWithAnotherDocument($mother->getData());
        }

        return $this->diff_with_mother;
    }

    public function isModifiedMother($hash_or_object, $key = null) {
        if(!$this->document->hasVersion()) {

            return false;
        }
        $hash = ($hash_or_object instanceof acCouchdbJson) ? $hash_or_object->getHash() : $hash_or_object;
        $hash .= ($key) ? "/".$key : null;

        return array_key_exists($hash, $this->document->getDiffWithMother());
    }

    protected function getDiffWithAnotherDocument(stdClass $document) {

        $other_json = new acCouchdbJsonNative($document);
        $current_json = new acCouchdbJsonNative($this->document->getData());

        return $current_json->diff($other_json);
    }

    public function getMaster() {
        if(is_null($this->master)) {
            $this->master = $this->document->findMaster();
        }

        return $this->master;
    }

    public function isMaster() {
		if (is_null($this->document->getMaster())) {
			return true;
		}
        return $this->document->getMaster()->get('_id') == $this->document->get('_id');
    }

    public function isRectifiable() {

        return $this->document->isVersionnable();
    }

    public function isModifiable() {

        return $this->document->isVersionnable();
    }

    public function isVersionnable() {

        return $this->document->isMaster();
    }

    public function needNextVersion() {
        if (!$this->document->getSuivante()) {

            return false;
        }

        if($this->document->isModificative()) {

            return $this->needNextModificative();
        }

        if($this->document->isRectificative()) {

            return $this->needNextRectificative();
        }

        return false;      
    }

    protected function needNextRectificative() {
        if (!$this->document->isRectificative()) {
           
           return false;
        }

        return $this->document->motherHasChanged();
    }

    protected function needNextModificative() {
        if (!$this->document->isModificative()) {
           
           return false;
        }

       return $this->document->motherHasChanged();
    }

    public function generateRectificative() {
        $document_rectificative = clone $this->document;

        if(!$this->isRectifiable()) {

            throw new sfException(sprintf('The document %s is not rectificable, maybe she was already rectificate', $this->document->get('_id')));
        }

        $document_rectificative->version = $this->buildVersionDocument($this->getRectificative() + 1, 0);
        $this->document->listenerGenerateVersion($document_rectificative);

        return $document_rectificative;
    }

    public function generateModificative() {
        $document_modificative = clone $this->document;

        if(!$this->isModifiable()) {

            throw new sfException(sprintf('The document %s is not modifiable, maybe she was already modified', $this->document->get('_id')));
        }

        $document_modificative->version = $this->buildVersionDocument($this->getRectificative(), $this->getModificative() + 1);
        $this->document->listenerGenerateVersion($document_modificative);

        return $document_modificative;
    }

    public function generateNextVersion()
    {
        if($this->document->isModificative()) {

            return $this->generateModificativeSuivante();
        }

        if($this->document->isRectificative()) {

            return $this->generateRectificativeSuivante();
        }

        return false;
    }

    public function generateRectificativeSuivante() {
        $next_document = $this->document->getSuivante();

        if(!$next_document) {

            return null;
        }
        
        $next_document_rectificative = $next_document->generateRectificative();
        $this->document->listenerGenerateNextVersion($next_document_rectificative);

        return $next_document_rectificative;
    }

    public function generateModificativeSuivante() {
        $next_document = $this->document->getSuivante();

        if(!$next_document) {

            return null;
        }
        
        $next_document_modificative = $next_document->generateModificative();
        $this->document->listenerGenerateNextVersion($next_document_modificative);

        return $next_document_modificative;
    }

}