<?php

class ValidatorEtablissement extends acValidatorCouchdbDocument
{
    public function __construct($options = array(), $messages = array())
    {
        parent::__construct($options, $messages);
        $this->options['filtres']['famille'] = $this->options['familles'];
    }

    protected function configure($options = array(), $messages = array())
    {
        parent::configure($options, $messages);
        $this->setOption('type', 'Etablissement');
        $this->setOption('prefix', '');
        $this->addOption('familles', array());
    }    
}