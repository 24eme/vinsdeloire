<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class CompteSocieteModificationForm
 * @author mathurin
 */
class CompteCoordonneeForm extends acCouchdbObjectForm {
    
    private $compte;
    private $reduct_rights = false;

    public function __construct(Compte $compte, $reduct_rights = false, $options = array(), $CSRFSecret = null) {
        $this->compte = $compte;
        $this->reduct_rights = $reduct_rights;
        parent::__construct($compte, $options, $CSRFSecret); 
        $this->defaults['pays'] = 'FR';   
    }

    public function configure() {
        parent::configure();
        if (!$this->reduct_rights) {
            $this->setWidget('adresse', new sfWidgetFormInput());
            $this->setWidget('adresse_complementaire', new sfWidgetFormInput());
            $this->setWidget('code_postal', new sfWidgetFormInput());
            $this->setWidget('commune', new sfWidgetFormInput());
            $this->setWidget('cedex', new sfWidgetFormInput());
            $this->setWidget('pays', new sfWidgetFormChoice(array('choices' => $this->getCountryList()), array('class' => 'autocomplete')));
            $this->setWidget('droits', new sfWidgetFormChoice(array('choices' => $this->getDroits(), 'multiple' => true, 'expanded' => true)));
        }

        $this->setWidget('email', new sfWidgetFormInput());
        $this->setWidget('telephone_perso', new sfWidgetFormInput());
        $this->setWidget('telephone_bureau', new sfWidgetFormInput());
        $this->setWidget('telephone_mobile', new sfWidgetFormInput());
        $this->setWidget('fax', new sfWidgetFormInput());
        $this->setWidget('site_internet', new sfWidgetFormInput());
        
        //   $this->setWidget('tags', new sfWidgetFormChoice(array('choices' => $this->getAllTags())));
        if (!$this->reduct_rights) {
            $this->widgetSchema->setLabel('adresse', 'N° et nom de rue *');
            $this->widgetSchema->setLabel('adresse_complementaire', 'Adresse complémentaire');
            $this->widgetSchema->setLabel('code_postal', 'CP *');
            $this->widgetSchema->setLabel('commune', 'Ville *');
            $this->widgetSchema->setLabel('cedex', 'Cedex');
            $this->widgetSchema->setLabel('pays', 'Pays *');
            $this->widgetSchema->setLabel('droits', 'Droits *');
        }
        $this->widgetSchema->setLabel('email', 'E-mail');
        $this->widgetSchema->setLabel('telephone_perso', 'Telephone Perso.');
        $this->widgetSchema->setLabel('telephone_bureau', 'Telephone Bureau');
        $this->widgetSchema->setLabel('telephone_mobile', 'Mobile');
        $this->widgetSchema->setLabel('fax', 'Fax');
        $this->widgetSchema->setLabel('site_internet', 'Site Internet');
        
        //    $this->widgetSchema->setLabel('tags', 'Tags');

        if (!$this->reduct_rights) {
            $this->setValidator('adresse', new sfValidatorString(array('required' => true)));
            $this->setValidator('adresse_complementaire', new sfValidatorString(array('required' => false)));
            $this->setValidator('code_postal', new sfValidatorString(array('required' => true)));
            $this->setValidator('commune', new sfValidatorString(array('required' => true)));
            $this->setValidator('cedex', new sfValidatorString(array('required' => false)));
            $this->setValidator('pays', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCountryList()))));
            $this->setValidator('droits', new sfValidatorChoice(array('required' => false, 'multiple' => true, 'choices' => array_keys($this->getDroits()))));
        }
        
        $this->setValidator('email', new sfValidatorString(array('required' => false)));
        $this->setValidator('telephone_perso', new sfValidatorString(array('required' => false)));
        $this->setValidator('telephone_bureau', new sfValidatorString(array('required' => false)));
        $this->setValidator('telephone_mobile', new sfValidatorString(array('required' => false)));
        $this->setValidator('fax', new sfValidatorString(array('required' => false)));
        $this->setValidator('site_internet', new sfValidatorString(array('required' => false)));
        
        //  $this->setValidator('tags', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getAllTags()))));

        if($this->compte->isNew())
                $this->widgetSchema['statut']->setAttribute('disabled', 'disabled');
        $this->widgetSchema->setNameFormat('compte_modification[%s]');
    }

    public function getDroits() {

        return array(Roles::CONTRAT => "Contrat");
    }
   
    public function getCountryList() {
        $destinationChoicesWidget = new sfWidgetFormI18nChoiceCountry(array('culture' => 'fr', 'add_empty' => true));
        $destinationChoices = $destinationChoicesWidget->getChoices();
        $destinationChoices['inconnu'] = 'Inconnu';
        return $destinationChoices;
    } 
    
    public function getAllTags() {
        return CompteClient::getInstance()->getAllTags();
    }    
    
    protected function doSave($con = null) {
        if (null === $con) {
            $con = $this->getConnection();
        }

        $this->updateObject();
        if($this->compte->isNew()){
            $this->compte->statut = CompteClient::STATUT_ACTIF;
        }
        if($this->compte->isSocieteContact())
        {
            $this->compte->statut = $this->compte->getSociete()->statut;
        }
        if($this->compte->isEtablissementContact()){
            $this->compte->statut = $this->compte->getEtablissement()->statut;
        }
        $this->object->getCouchdbDocument()->save();
    }
    
}