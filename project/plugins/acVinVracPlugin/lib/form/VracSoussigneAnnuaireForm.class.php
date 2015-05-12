<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneForm
 * @author mathurin
 */
class VracSoussigneAnnuaireForm extends VracSoussigneForm {


   
    public function configure()
    {
    	$this->disableCSRFProtection();
        if ($this->getObject()->isTeledeclare() && $this->getObject()->createur_identifiant) {
        	$vendeurs = $this->getRecoltants();
        	$acheteurs = $this->getNegociants();
        	$commerciaux = $this->getCommerciaux();
        	$this->setWidget('vendeur_identifiant', new sfWidgetFormChoice(array('choices' => $vendeurs), array('class' => 'autocomplete')));
        	$this->setWidget('acheteur_identifiant', new sfWidgetFormChoice(array('choices' => $acheteurs), array('class' => 'autocomplete')));
        	$this->setWidget('commercial', new sfWidgetFormChoice(array('choices' => $commerciaux), array('class' => '')));
                $this->setValidator('vendeur_identifiant', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($vendeurs))));
        	$this->setValidator('acheteur_identifiant', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($acheteurs))));
        	$this->setValidator('commercial', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($commerciaux))));
        } else {
        	$this->setWidget('vendeur_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' => EtablissementFamilles::FAMILLE_PRODUCTEUR)));
            $this->setWidget('acheteur_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire','familles' =>  EtablissementFamilles::FAMILLE_NEGOCIANT)));
            $this->setValidator('vendeur_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => EtablissementFamilles::FAMILLE_PRODUCTEUR)));
        	$this->setValidator('acheteur_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => EtablissementFamilles::FAMILLE_NEGOCIANT)));
        }
        
        
        $this->setWidget('interne', new sfWidgetFormInputCheckbox());
        
        $this->setWidget('mandataire_exist', new sfWidgetFormInputCheckbox());        
        
        $this->setWidget('mandatant', new sfWidgetFormChoice(array('expanded' => true, 'multiple'=> true , 'choices' => VracClient::getInstance()->getMandatants())));
                
        $this->setWidget('mandataire_identifiant', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' =>  EtablissementFamilles::FAMILLE_COURTIER)));
        
        $this->widgetSchema->setLabels(array(
            'vendeur_famille' => '',
            'vendeur_identifiant' => 'Sélectionner un vendeur :',
            'acheteur_famille' => '',
            'acheteur_identifiant' => 'Sélectionner un acheteur :',
            'interne' => 'Cocher si le contrat est interne',
            'mandataire_identifiant' => 'Sélectionner un courtier :',
            'mandataire_exist' => "Décocher s'il n'y a pas de mandataire",
            'mandatant' => 'Mandaté par : '
        ));
        
        $this->setValidator('interne', new sfValidatorBoolean(array('required' => false)));
        $this->setValidator('mandataire_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => EtablissementFamilles::FAMILLE_COURTIER)));
        $this->setValidator('mandataire_exist', new sfValidatorBoolean(array('required' => false)));
        $this->setValidator('mandatant', new sfValidatorChoice(array('required' => false,'multiple'=> true, 'choices' => array_keys(VracClient::getInstance()->getMandatants()))));
        
        $this->validatorSchema['vendeur_identifiant']->setMessage('required', 'Le choix d\'un vendeur est obligatoire');        
        $this->validatorSchema['acheteur_identifiant']->setMessage('required', 'Le choix d\'un acheteur est obligatoire');             
        
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
    public function doUpdateObject($values) {
        if(isset($values['mandataire_exist']) && !$values['mandataire_exist'])
        {
            $values['mandataire_identifiant'] = null;
        }
        if(!isset($values['mandataire_identifiant']) || !$values['mandataire_identifiant'])
        {
            $values['mandatant'] = null;
            $values['mandataire_exist'] = false;
        }
        if ($values['commercial']) {
            $this->getObject()->storeInterlocuteurCommercialInformations($values['commercial'], $this->getAnnuaire()->commerciaux->get($values['commercial']));
    	} else {
    		$this->getObject()->remove('interlocuteur_commercial');
    		$this->getObject()->add('interlocuteur_commercial');
    	}
        parent::doUpdateObject($values);
        $this->getObject()->setInformations();
    }
    
	public function getUpdatedVrac()
  	{
  		$this->doUpdateObject($this->getValues());
    	return $this->getObject();
  	}
}

