<?php

class DRMCommentaireForm extends acCouchdbObjectForm {

    public function configure() {
      parent::configure();
      $this->setWidget('commentaire', new sfWidgetFormTextarea(array(), array('style' => 'width: 100%;resize:none;')));
      $this->setValidator('commentaire', new sfValidatorString(array('required' => false)));
      $this->widgetSchema->setLabel('commentaire', 'Commentaires :');
      
      $this->widgetSchema->setNameFormat('drm[%s]');
    }

}
