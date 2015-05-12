<?php

class acValidatorCouchdbDocument extends sfValidatorBase
{

    protected $document = null;

    public function __construct($options = array(), $messages = array())
    {
        parent::__construct($options, $messages);
        $this->options['filtres']['type'] = $this->options['type'];
    }

    protected function configure($options = array(), $messages = array())
    {
        $this->addRequiredOption('type');
        $this->addOption('prefix', '');
        $this->addOption('filtres', array());

        $this->addMessage('exist', "The couchdb document %id% doesn't exist");
        $this->setMessage('invalid', "The couchdb document %id% doesn't fit the filters %filters%");
    }

    protected function doClean($value)
    {
        $id = $this->getOption('prefix').$value;
        $this->document = acCouchdbManager::getClient()->find($this->getOption('prefix').$value);

        if (!$this->document) {
            throw new sfValidatorError($this, 'exist', array('id' => $id));
        }

        if (!$this->filtre($this->document, $this->getOption('filtres'))) {
	  throw new sfValidatorError($this, 'invalid', array('id' => $id, 'filters' => json_encode($this->getOption('filtres'))));
        }

        return $value;
    }

    public function getDocument() {

        return $this->document;
    }

    protected function filtre($document, $filtres) {
        foreach($filtres as $hash => $filtre) {
            if (!$document->exist($hash)) {
                return false;
            }

            if (is_array($filtre)) {
                $values = $filtre;
            } else {
                $values = array($filtre);
            }

            foreach ($values as $value) {
                if($document->get($hash) != $value) {
                    return false;
                }
            }
            
        }

        return true;
    }
}