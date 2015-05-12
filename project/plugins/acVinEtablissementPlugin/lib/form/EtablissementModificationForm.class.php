<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class SocieteModificationForm
 * @author mathurin
 */
class EtablissementModificationForm extends CompteCoordonneeSameSocieteForm {

    private $etablissement;
    private $liaisons_operateurs = null;

    public function __construct(Etablissement $etablissement, $options = array(), $CSRFSecret = null) {
        $this->etablissement = $etablissement;
        $this->liaisons_operateurs = $etablissement->liaisons_operateurs;
        parent::__construct($etablissement, $options, $CSRFSecret);
    }

    public function configure() {
        parent::configure();
        $this->setWidget('nom', new sfWidgetFormInput());
        $this->setWidget('statut', new sfWidgetFormChoice(array('choices' => $this->getStatuts(), 'multiple' => false, 'expanded' => true)));
        $this->setWidget('region', new sfWidgetFormChoice(array('choices' => $this->getRegions())));
        $this->embedForm('liaisons_operateurs', new LiaisonsItemForm($this->getObject()->liaisons_operateurs));
        $this->setWidget('no_accises', new sfWidgetFormInput());
        $this->setWidget('commentaire', new sfWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));
        $this->setWidget('site_fiche', new sfWidgetFormInput());


        $this->widgetSchema->setLabel('nom', 'Nom du chai *');
        $this->widgetSchema->setLabel('statut', 'Statut *');
        $this->widgetSchema->setLabel('region', 'Région viticole *');
        $this->widgetSchema->setLabel('no_accises', "N° d'Accise");
        $this->widgetSchema->setLabel('commentaire', 'Commentaire');
        $this->widgetSchema->setLabel('site_fiche', 'Site Fiche Publique');



        $this->setValidator('nom', new sfValidatorString(array('required' => true)));
        $this->setValidator('statut', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getStatuts()))));
        $this->setValidator('region', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getRegions()))));
        $this->setValidator('site_fiche', new sfValidatorString(array('required' => false)));
        $this->setValidator('no_accises', new sfValidatorString(array('required' => false)));
        $this->setValidator('commentaire', new sfValidatorString(array('required' => false)));


        if (!$this->etablissement->isCourtier()) {
            $recette_locale = $this->getRecettesLocales();
            $this->setWidget('cvi', new sfWidgetFormInput());
            $this->setWidget('relance_ds', new sfWidgetFormChoice(array('choices' => $this->getOuiNonChoices())));
            $this->setWidget('recette_locale_choice', new sfWidgetFormChoice(array('choices' => $recette_locale)));
            $this->widgetSchema->setLabel('cvi', 'CVI');
            $this->widgetSchema->setLabel('relance_ds', 'Relance DS *');            
            $this->widgetSchema->setLabel('recette_locale_choice', 'Recette Locale *');
            $this->setValidator('cvi', new sfValidatorString(array('required' => false)));
            $this->setValidator('relance_ds', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getOuiNonChoices()))));            
            $this->setValidator('recette_locale_choice', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recette_locale))));
            
            if (!$this->etablissement->isNegociant()) {
                $this->setWidget('raisins_mouts', new sfWidgetFormChoice(array('choices' => $this->getOuiNonChoices())));
                $this->setWidget('exclusion_drm', new sfWidgetFormChoice(array('choices' => $this->getOuiNonChoices())));
                $this->setWidget('type_dr', new sfWidgetFormChoice(array('choices' => $this->getTypeDR())));
                $this->widgetSchema->setLabel('raisins_mouts', 'Raisins et Moûts *');
                $this->widgetSchema->setLabel('exclusion_drm', 'Exclusion DRM *');
                $this->widgetSchema->setLabel('type_dr', 'Type de DR *');
                $this->setValidator('raisins_mouts', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getOuiNonChoices()))));
                $this->setValidator('exclusion_drm', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getOuiNonChoices()))));
                $this->setValidator('type_dr', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getTypeDR()))));
            }
        } else {
            $this->setWidget('carte_pro', new sfWidgetFormInput());
            $this->widgetSchema->setLabel('carte_pro', 'N° Carte professionnelle');
            $this->setValidator('carte_pro', new sfValidatorString(array('required' => false)));
        }
        
        if($this->etablissement->isNew()) {
            $this->widgetSchema['statut']->setAttribute('disabled', 'disabled');
        }

        $this->widgetSchema->setNameFormat('etablissement_modification[%s]');
    }

    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();

        $this->setDefault('recette_locale_choice', $this->getObject()->recette_locale->id_douane);
    }

    public function getStatuts() {
        
        return EtablissementClient::getStatuts();
    }

    public function getOuiNonChoices() {
        
        return array(EtablissementClient::OUI => 'Oui', EtablissementClient::NON => 'Non');
    }

    public function getAdresseSociete() {
        
        return array(1 => 'Oui', 0 => 'Non');
    }
    
    public function getRegions() {
        return EtablissementClient::getRegions();
    }

    public function getTypeDR() {
        return EtablissementClient::getTypeDR();
    }

    protected function doSave($con = null) {
        if (null === $con) {
            $con = $this->getConnection();
        }
        $this->updateObject();
        
        $this->etablissement->remove('liaisons_operateurs');
        $this->etablissement->add('liaisons_operateurs');
        
        foreach ($this->getEmbeddedForms() as $key => $form) {

            foreach ($this->values[$key] as $liaison) {
                $this->etablissement->addLiaison($liaison['type_liaison'], EtablissementClient::getInstance()->find($liaison['id_etablissement']));
            }
        }
        if($this->values['recette_locale_choice']){
            $this->etablissement->recette_locale->id_douane = $this->values['recette_locale_choice'];
        }
        
        $old_compte = $this->etablissement->compte;
        $switch = false;
         if($this->values['adresse_societe'] && !is_null($this->values['statut']) && !$this->etablissement->getSociete()->isManyEtbPrincipalActif()
            && ($this->values['statut'] != ($socStatut = $this->etablissement->getSociete()->statut))){
                throw new sfException("Il s'agit de l'établissement pricipal de la société, il ne peut être suspendu. Pour le suspendre, vous devez suspendre la société.");
        }
        if($this->values['adresse_societe'] && !$this->etablissement->isSameContactThanSociete()){           
           $this->etablissement->compte = $this->etablissement->getSociete()->compte_societe;
           $switch = true;
        } elseif(!$this->values['adresse_societe'] && $this->etablissement->isSameContactThanSociete()) {
           $this->etablissement->compte = null;
           $switch = true;
        }
        $this->etablissement->save();
        
        if($switch) {
            $this->etablissement->switchOrigineAndSaveCompte($old_compte);
            $this->etablissement->save();
        }
    }

    public function bind(array $taintedValues = null, array $taintedFiles = null) {
        foreach ($this->embeddedForms as $key => $form) {
            if ($form instanceof LiaisonsItemForm) {
                if (isset($taintedValues[$key])) {
                    $form->bind($taintedValues[$key], $taintedFiles[$key]);
                    $this->updateEmbedForm($key, $form);
                }
            }
        }

        if(!array_key_exists('statut', $taintedValues)) {
            $taintedValues['statut'] = $this->getObject()->statut;
        }

        parent::bind($taintedValues, $taintedFiles);
    }

    public function updateEmbedForm($name, $form) {
        $this->widgetSchema[$name] = $form->getWidgetSchema();
        $this->validatorSchema[$name] = $form->getValidatorSchema();
    }

    public function getFormTemplate() {
        $etablissement = new Etablissement();
        $form_embed = new LiaisonItemForm($etablissement->liaisons_operateurs->add());
        $form = new EtablissementCollectionTemplateForm($this, 'liaisons_operateurs', $form_embed);
        return $form->getFormTemplate();
    }

    protected function unembedForm($key) {
        unset($this->widgetSchema[$key]);
        unset($this->validatorSchema[$key]);
        unset($this->embeddedForms[$key]);
        $this->liaisons_operateurs->remove($key);
    }

    protected function getRecettesLocales() {        
        $douanes = SocieteAllView::getInstance()->findByInterproAndStatut('INTERPRO-inter-loire', SocieteClient::STATUT_ACTIF, array(SocieteClient::SUB_TYPE_DOUANE));

        $douanesList = array();
        foreach ($douanes as $key => $douane) {
            $douaneObj = SocieteClient::getInstance()->find($douane->id);            
            $douanesList[$douane->id] = $douane->key[SocieteAllView::KEY_RAISON_SOCIALE].' '.$douaneObj->siege->commune.' ('.$douaneObj->siege->code_postal.')';
        }
        return $douanesList;    
    }


}

?>
