<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracEtiquettesForm
 * @author mathurin
 */
class VracEtiquettesForm extends sfForm {


    public function __construct($defaults = array(), $options = array(), $CSRFSecret = null) {
        $defaults['date_debut'] = date('d/m/Y');
        $defaults['date_fin'] = date('d/m/Y');
        parent::__construct($defaults, $options, $CSRFSecret);
    }
    
    public function configure() {
        parent::configure();
        $this->setWidget('date_debut', new sfWidgetFormInput());
        $this->widgetSchema->setLabel('date_debut','Date de début : ');
        
        
        $this->setWidget('date_fin', new sfWidgetFormInput());
        $this->widgetSchema->setLabel('date_fin', 'Date de fin : ');

        $this->setValidator('date_debut', new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date de dépot en mairie doit être jj/mm/aaaa')));
        $this->setValidator('date_fin', new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date de dépot en mairie doit être jj/mm/aaaa')));

        $this->widgetSchema->setNameFormat('vrac_vignettes[%s]');
    }

}