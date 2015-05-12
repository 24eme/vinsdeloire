<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class CompteExtendedModificationForm
 * @author mathurin
 */
class CompteForm extends CompteCoordonneeSameSocieteForm {

    public function __construct(Compte $compte, $options = array(), $CSRFSecret = null) {
        parent::__construct($compte, $options, $CSRFSecret);         
    }

    public function configure() {
        parent::configure();
        $this->setWidget('statut', new sfWidgetFormChoice(array('choices' => $this->getStatuts(), 'multiple' => false, 'expanded' => true)));
        $this->setWidget('civilite', new sfWidgetFormChoice(array('choices' => $this->getCiviliteList())));
        $this->setWidget('nom', new sfWidgetFormInput());
        $this->setWidget('prenom', new sfWidgetFormInput());
        $this->setWidget('fonction', new sfWidgetFormInput());
        $this->setWidget('commentaire', new sfWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));
        

        $this->widgetSchema->setLabel('statut', 'Statut *');
        $this->widgetSchema->setLabel('civilite', 'Civilite *');
        $this->widgetSchema->setLabel('nom', 'Nom *');
        $this->widgetSchema->setLabel('prenom', 'Prenom');
        $this->widgetSchema->setLabel('fonction', 'Fonction *');
        $this->widgetSchema->setLabel('commentaire', 'Commentaire');

        $this->setValidator('statut', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getStatuts()))));
        $this->setValidator('civilite', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCiviliteList()))));
        $this->setValidator('nom', new sfValidatorString(array('required' => true)));
        $this->setValidator('prenom', new sfValidatorString(array('required' => false)));
        $this->setValidator('fonction', new sfValidatorString(array('required' => true)));
        $this->setValidator('commentaire', new sfValidatorString(array('required' => false)));

        $this->widgetSchema->setNameFormat('compte_modification[%s]');
    }

    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();

        if($this->getObject()->isNew()){
            $this->setDefault('statut', $this->getObject()->getSociete()->statut);
        } 
    }

    protected function doSave($con = null) {
        parent::dosave();
        $this->getObject()->doSameCoordonneeThanSocieteAndSave($this->values['adresse_societe']);
        $this->getObject()->getCouchdbDocument()->save();
    }

    public function getCiviliteList() {
        return array('Mme' => 'Mme', 'M' => 'M');
    }


    public function getStatuts() {
        return EtablissementClient::getStatuts();
    }

}