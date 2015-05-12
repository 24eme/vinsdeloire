<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracSoussigneForm
 * @author mathurin
 */
class VracConditionForm extends acCouchdbObjectForm {

    private $types_contrat = array(VracClient::TYPE_CONTRAT_SPOT => 'Spot',
        VracClient::TYPE_CONTRAT_PLURIANNUEL => 'Pluriannuel');
    private $prix_variable = array('1' => 'Oui',
        '0' => 'Non');
    private $cvo_nature = array(VracClient::CVO_NATURE_MARCHE_DEFINITIF => 'Marché définitif',
        VracClient::CVO_NATURE_COMPENSATION => 'Compensation',
        VracClient::CVO_NATURE_NON_FINANCIERE => 'Non financière',
        VracClient::CVO_NATURE_VINAIGRERIE => 'Vinaigrerie');
    protected $isTeledeclarationMode;

    public function __construct(Vrac $vrac, $isTeledeclarationMode = false, $options = array(), $CSRFSecret = null) {
        $this->isTeledeclarationMode = $isTeledeclarationMode;
        parent::__construct($vrac, $options, $CSRFSecret);
    }

    public function configure() {
        $this->setWidget('type_contrat', new sfWidgetFormChoice(array('choices' => $this->getTypesContrat(), 'expanded' => true)));
        $this->setWidget('prix_variable', new sfWidgetFormChoice(array('choices' => $this->getPrixVariable(), 'expanded' => true)));
        $this->setWidget('part_variable', new sfWidgetFormInput());
        $this->setWidget('commentaire', new sfWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));

        $this->widgetSchema->setLabels(array(
            'type_contrat' => 'Type de contrat',
            'prix_variable' => 'Partie de prix variable ?',
            'part_variable' => 'Part du prix variable sur la quantité',
            'commentaire' => 'Commentaires :',
        ));

        $cvo_repartion_notchangeable = !is_null($this->getObject()->volume_enleve) && $this->getObject()->volume_enleve > 0;
        if ($cvo_repartion_notchangeable)
            $this->widgetSchema['cvo_repartition']->setAttribute('disabled', 'disabled');

        $dateRegexpOptions = array('required' => true,
            'pattern' => "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",
            'min_length' => 10,
            'max_length' => 10);
        $dateRegexpErrors = array('required' => 'Cette obligatoire',
            'invalid' => 'Date invalide (le format doit être jj/mm/aaaa)',
            'min_length' => 'Date invalide (le format doit être jj/mm/aaaa)',
            'max_length' => 'Date invalide (le format doit être jj/mm/aaaa)');

        $this->setValidators(array(
            'type_contrat' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getTypesContrat()))),
            'prix_variable' => new sfValidatorInteger(array('required' => true)),
            'part_variable' => new sfValidatorNumber(array('required' => false, 'max' => 50, 'min' => 0), array('max' => 'Part variable %max% max.',
                'min' => 'Part variable %min% min.')),
            'commentaire' => new sfValidatorString(array('required' => false)),
        ));



        if (!$this->isTeledeclarationMode) {
            $this->setWidget('cvo_nature', new sfWidgetFormChoice(array('choices' => $this->getCvoNature())));
            $this->getWidget('cvo_nature')->setLabel("Nature de la transaction");
            $this->setValidator('cvo_nature', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCvoNature()))));

            $this->setWidget('cvo_repartition', new sfWidgetFormChoice(array('choices' => $this->getCvoRepartition())));
            $this->getWidget('cvo_repartition')->setLabel("Répartition de la CVO");
            $this->setValidator('cvo_repartition', new sfValidatorChoice(array('required' => !$cvo_repartion_notchangeable, 'choices' => array_keys($this->getCvoRepartition()))));

            $this->setWidget('date_signature', new sfWidgetFormInput());
            $this->getWidget('date_signature')->setLabel("Date de signature");
            $this->setValidator('date_signature', new sfValidatorRegex($dateRegexpOptions, $dateRegexpErrors));

            $this->setWidget('date_campagne', new sfWidgetFormInput());
            $this->getWidget('date_campagne')->setLabel("Date de campagne");
            $this->setValidator('date_campagne', new sfValidatorRegex($dateRegexpOptions, $dateRegexpErrors));
        }

        if ($this->getObject()->isTeledeclare()) {
            $this->setWidget('enlevement_date', new sfWidgetFormInput());
            $this->getWidget('enlevement_date')->setLabel("Date d'enlèvement");
            $this->setValidator('enlevement_date', new sfValidatorString(array('required' => false)));

            $this->setWidget('enlevement_frais_garde', new sfWidgetFormInputFloat());
            $this->getWidget('enlevement_frais_garde')->setLabel("Frais de garde");
            $this->setValidator('enlevement_frais_garde', new sfValidatorNumber(array('required' => false)));
        }

        $this->widgetSchema->setNameFormat('vrac[%s]');
    }

    public function getTypesContrat() {
        return $this->types_contrat;
    }

    public function getPrixVariable() {
        return $this->prix_variable;
    }

    public function getCvoNature() {
        return $this->cvo_nature;
    }

    public function getCvoRepartition() {
        $repartition = VracClient::$cvo_repartition;
        if ($this->getObject()->getAcheteurObject()->isInterLoire()) {
            return $repartition;
        }
        unset($repartition['50']);
        return $repartition;
    }

    public function doUpdateObject($values) {
        if ($values['type_contrat'] == VracClient::TYPE_CONTRAT_SPOT)
            $values['prix_variable'] = 0;

        parent::doUpdateObject($values);
        if ($this->isTeledeclarationMode)
            if (!$values['enlevement_date']) {
                $this->getObject()->add('enlevement_date', Date::addDelaiToDate('+35 days', date('Y-m-d')));
            } else {
                $this->getObject()->add('enlevement_date', Date::getIsoDateFromFrenchDate($values['enlevement_date']));
            }
    }

    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();        
        if ($this->getObject()->exist('enlevement_date') && $this->getObject()->enlevement_date) {
            $this->setDefault('enlevement_date', Date::francizeDate($this->getObject()->enlevement_date));
        }
    }

}
