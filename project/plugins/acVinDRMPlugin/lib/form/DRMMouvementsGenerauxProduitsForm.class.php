<?php

class DRMMouvementsGenerauxProduitsForm extends acCouchdbForm {

        public function __construct(DRM $drm) {
                $defaults = array("pas_de_mouvement" => !$drm->declaration->hasMouvementCheck());
                parent::__construct($drm, $defaults, array(), null);
        }

	public function configure() {
                $this->setWidgets(array(
                		'pas_de_mouvement' => new sfWidgetFormInputCheckbox()
                ));
                $this->widgetSchema->setLabels(array(
                		'pas_de_mouvement' => 'Pas de mouvement '
                ));

                $this->setValidators(array(
                		'pas_de_mouvement' => new sfValidatorBoolean(array('required' => false))
                ));

                $this->widgetSchema->setNameFormat('produits[%s]');
        }

}