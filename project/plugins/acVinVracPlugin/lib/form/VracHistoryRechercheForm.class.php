<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracHistoryRechercheForm
 * @author mathurin
 */
class VracHistoryRechercheForm extends sfForm {     
    
    
    private $societe;
    private $campagne;
     private $etablissement;
    
    public function __construct(Societe $societe, $etablissement, $campagne, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->societe = $societe;
        $this->campagne = $campagne;
        $this->etablissement = $etablissement;
        $defaults['campagne'] = $this->campagne;        
        $defaults['etablissement'] = $this->etablissement;
        parent::__construct($defaults, $options, $CSRFSecret);
    }
    
    public function configure()
    {
        $this->setWidget('campagne',new sfWidgetFormChoice(array('choices' => $this->getCampagnes(),'expanded' => false)));     
        $this->setWidget('etablissement', new sfWidgetFormChoice(array('choices' => $this->getEtablissements(),'expanded' => false)));      
                
        $this->widgetSchema->setLabels(array(
            'campagne' => 'Campagne',
            'etablissement' => 'Etablissement'));
        
        $this->setValidators(array(
            'campagne' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getCampagnes()))),
            'etablissement' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getEtablissements())))));
                
    }
    
    private function getCampagnes()
    {
        return array_merge(array('all' => 'Toutes les campagnes'),VracClient::getInstance()->listCampagneBySocieteId($this->societe->identifiant));
    }
    
    private function getEtablissements()
    {
        $etablissements = $this->societe->getEtablissementsObj();
        
        $etbArr = array();
        $etbArr['all'] = 'Tous les établissements';
        foreach ($etablissements as $id => $etbObj) {
            $etbArr[$etbObj->etablissement->identifiant] = $etbObj->etablissement->getDenomination();
        }
        return $etbArr;
    }
}

