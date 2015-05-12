<?php

class VracAjoutContratForm extends acCouchdbObjectForm 
{
	protected $_contrat_choices;
	
    public function configure() 
    {
        $this->setWidgets(array(
            'vrac' => new sfWidgetFormChoice(array('choices' => $this->getContratChoices())),
        	'volume' => new sfWidgetFormInputFloat(),
        ));
        $this->widgetSchema->setLabels(array(
        	'vrac' => 'Contrat*: ',
        	'volume' => 'Volume*: ',
        ));
        $this->setValidators(array(
            'vrac' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getContratChoices())), array('required' => 'Champ obligatoire')),
        	'volume' => new sfValidatorNumber(array('required' => true)),
        ));
        $this->widgetSchema->setNameFormat('vrac_ajout'.$this->getObject()->getIdentifiantHTML().'[%s]');
    }

    public function doUpdateObject($values) {
    	$this->getObject()->vrac->add($values['vrac'])->volume = $values['volume'];
    }
    
    public function getContratChoices() 
    {
      if (is_null($this->_contrat_choices)) {
	$etablissement = $this->getObject()->getDocument()->identifiant;
	$this->_contrat_choices = array();
	foreach (VracClient::getInstance()->retrieveFromEtablissementsAndHash('ETABLISSEMENT-'.$etablissement, $this->getObject()->getHash()) as $contrat) {
	  if (!$this->getObject()->vrac->exist($contrat->numero_contrat))
	    $this->_contrat_choices[$contrat->numero_contrat] = $contrat->numero_contrat;
	}
      }
      return $this->_contrat_choices;
    }

}