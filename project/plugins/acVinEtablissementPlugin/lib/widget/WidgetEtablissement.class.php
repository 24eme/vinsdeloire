<?php 

class WidgetEtablissement extends sfWidgetFormChoice
{
    protected $identifiant = null;

    public function __construct($options = array(), $attributes = array())
    {
        parent::__construct($options, $attributes);

        $this->setAttribute('data-ajax', $this->getUrlAutocomplete());
        $this->setOption('choices', $this->getChoices());
    }

    protected function configure($options = array(), $attributes = array())
    {
        parent::configure($options, $attributes);

        $this->setOption('choices', array());
        $this->addOption('familles', array());
        $this->addRequiredOption('interpro_id', null);
        $this->setAttribute('class', 'autocomplete'); 
    }

    public function setOption($name, $value) {
        parent::setOption($name, $value);

        if($name == 'familles') {
            $this->setAttribute('data-ajax', $this->getUrlAutocomplete());
        }

        return $this;
    }

    public function getUrlAutocomplete() {
        $familles = $this->getOption('familles');
		$interpro_id = $this->getOption('interpro_id');
        if (!is_array($familles) && $familles) {
            $familles = array($familles);
        }

        if (is_array($familles) && count($familles) > 0) {
            
            return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_byfamilles', array('interpro_id' => $interpro_id, 'familles' => implode("|",$familles)));
        }

        return sfContext::getInstance()->getRouting()->generate('etablissement_autocomplete_all', array('interpro_id' => $interpro_id));
    }

    public function getChoices() {
        if(!$this->identifiant) {

            return array();
        }
        $etablissements = EtablissementAllView::getInstance()->findByEtablissement($this->identifiant);
        if (!$etablissements) {

            return array();
        }
        
        $choices = array();
        foreach($etablissements as $key => $etablissement) {
            $choices[EtablissementClient::getInstance()->getId($etablissement->id)] = EtablissementAllView::getInstance()->makeLibelle($etablissement);
        }

        return $choices;
    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $this->identifiant = $value;

        return parent::render($name, $value, $attributes, $errors);
    }

}
