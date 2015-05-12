<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneModificationForm
 * @author mathurin
 */
class VracSoussigneModificationForm extends acCouchdbObjectForm {
   
    public function configure()
    {      
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_PRODUCTEUR) $this->configureAcheteurVendeur('vendeur');
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_NEGOCIANT) $this->configureAcheteurVendeur('acheteur');
        if($this->getObject()->famille == EtablissementFamilles::FAMILLE_COURTIER) $this->configureMandataire();

        $formSiege = new VracSoussigneModificationSiegeForm($this->getObject()->siege);
        $this->embedForm('siege', $formSiege);

        $this->widgetSchema->setNameFormat('vrac[%s]');    
    }

    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $this->setDefault('no_tva_intracommunautaire', $this->getObject()->getSociete()->getNoTvaIntracommunautaire());
    }
    
    private function configureAcheteurVendeur($label)
    {         
        $this->setWidget('no_accises', new sfWidgetFormInput()); 
        $this->setWidget('no_tva_intracommunautaire', new sfWidgetFormInput());
            
        $this->widgetSchema->setLabels(array(
            'no_accises' => 'N° ACCISE',
            'no_tva_intracommunautaire' => 'TVA Intracomm.'
        ));
                
        $this->setValidators(array(
            'no_accises' => new sfValidatorString(array('required' => false)),
            'no_tva_intracommunautaire' => new sfValidatorString(array('required' => false))
        )); 
    }
    
    private function configureMandataire() {
                
        $this->setWidget('carte_pro', new sfWidgetFormInput());          
        
        $this->widgetSchema->setLabels(array(
            'carte_pro' => 'N° carte professionnelle',
        ));
        
       $this->setValidators(
       array(
            'carte_pro' => new sfValidatorNumber(array('required' => false)),
            ));
           
    }

    protected function doSave($con = null) {
        parent::doSave($con);

        $societe = $this->getObject()->getSociete();

        if($societe) {
            $societe->no_tva_intracommunautaire = $this->getValue('no_tva_intracommunautaire');
            $societe->save();
        }
        
        if(!$this->getObject()->isSameContactThanSociete()){
            $this->getObject()->getMasterCompte()->updateAndSaveCoordoneesFromEtablissement($this->getObject());
        }
        else
        {
            throw new sfException("Les modifications ne peuvent être effectuées car cet etablissement à le même compte que la société.");
        }
    } 

}


