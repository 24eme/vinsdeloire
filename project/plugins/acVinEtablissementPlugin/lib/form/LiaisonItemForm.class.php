<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class LiaisonItemForm
 * @author mathurin
 */
class LiaisonItemForm extends acCouchdbObjectForm {


    public function configure() {

        $this->setWidget('type_liaison', new sfWidgetFormChoice(array('choices' => $this->getTypesLiaisons())));
        $this->setWidget('id_etablissement', new WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire'), array('class' => 'autocomplete')));

        $this->widgetSchema->setLabel('type_liaison', 'Type de liaison (externe)');
        $this->widgetSchema->setLabel('id_etablissement', 'Établissement');

        $this->setValidator('type_liaison', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getTypesLiaisons()))));

        $this->setValidator('id_etablissement', new ValidatorEtablissement(array('required' => true)));

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->widgetSchema->setNameFormat('liaison[%s]');
    }

    public function getTypesLiaisons() {
        return EtablissementClient::getTypesLiaisons();
    }
}

?>
