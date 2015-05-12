<?php
class ProduitDefinitionForm extends acCouchdbObjectForm {

    public function configure() {
    	$this->setWidgets(array(
            'libelle' => new sfWidgetFormInputText(),
			'format_libelle' => new sfWidgetFormInputText(),
            'code' => new sfWidgetFormInputText(),
    	));
		$this->widgetSchema->setLabels(array(
            'libelle' => 'Libellé :',
			'format_libelle' => 'Format du Libellé:',
			'code' => 'Code :',
		));
		$this->setValidators(array(
            'libelle' => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')),
			'format_libelle' => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')),
			'code' => new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')),
		));

        $this->widgetSchema->setHelp('code', "Ce code est pour inter-loire (il en général identique à la clé sauf pour les couleurs)");

        if($this->getObject()->hasCodes()) {
            $this->setWidget('code_produit', new sfWidgetFormInputText()); 
            $this->setWidget('code_douane', new sfWidgetFormInputText());
            $this->setWidget('code_comptable', new sfWidgetFormInputText());

            $this->getWidget('code_produit')->setLabel("Code produit :");
            $this->getWidget('code_douane')->setLabel("Code douane :");
            $this->getWidget('code_comptable')->setLabel("Code comptable :");

            $this->setValidator('code_produit', new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')));
            $this->setValidator('code_douane', new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')));
            $this->setValidator('code_comptable', new sfValidatorString(array('required' => false), array('required' => 'Champ obligatoire')));
        }

        if($this->getObject()->exist('densite')) {
            $this->setWidget('densite', new sfWidgetFormInputText()); 
            $this->getWidget('densite')->setLabel("Densité :");
            $this->setValidator('densite', new sfValidatorString(array('required' => true), array('required' => 'Champ obligatoire')));
            $this->widgetSchema->setHelp('densite', "La densité par défaut est de 1.3, celle des crémant est de 1.5");
        }
		
		if ($this->getObject()->hasDepartements()) {
			$this->embedForm(
				'secteurs', 
				new ProduitDepartementCollectionForm(null, array('departements' => $this->getNoeudDepartement(), 'nb' => $this->getOption('nbDepartement', null)))
			);
		}
        if($this->getObject()->hasDroit(ConfigurationDroits::DROIT_DOUANE)) {
    		$nbDouane = (count($this->getNoeudDroit(ConfigurationDroits::DROIT_DOUANE)) > 0)? count($this->getNoeudDroit(ConfigurationDroits::DROIT_DOUANE)) : 1;
			$this->embedForm(
				'droit_douane', 
				new ProduitDroitCollectionForm(null, array('droits' => $this->getNoeudDroit('douane'), 'nb' => $this->getOption('nbDouane', null)))
			);
        }
        if($this->getObject()->hasDroit(ConfigurationDroits::DROIT_CVO)) {
            $nbCvo = (count($this->getNoeudDroit(ConfigurationDroits::DROIT_CVO)) > 0)? count($this->getNoeudDroit(ConfigurationDroits::DROIT_CVO)) : 1;
			$this->embedForm(
				'droit_cvo', 
				new ProduitDroitCollectionForm(null, array('droits' => $this->getNoeudDroit(ConfigurationDroits::DROIT_CVO), 'nb' => $this->getOption('nbCvo', null)))
			);
        }
		if ($this->getObject()->hasLabels()) {
			$this->embedForm(
				'labels', 
				new ProduitLabelCollectionForm(null, array('labels' => $this->getNoeudLabel(), 'nb' => $this->getOption('nbLabel', null)))
			);
		}
		if ($this->getObject()->hasDetails()) {
			$this->embedForm(
				'detail', 
				new ProduitDetailsForm($this->getObject()->getOrAdd('detail'))
			);
		}
        $this->widgetSchema->setNameFormat('produit_definition[%s]');
        $this->mergePostValidator(new ProduitDefinitionValidatorSchema($this->getObject()));
    }

    private function getNoeudInterpro($object = null)
    {
    	if (!$object) {
    		$object = $this->getObject();
    	}
    	return $object->interpro->getOrAdd('INTERPRO-inter-loire');
    }
    
    private function getNoeudDepartement($object = null)
    {
    	if (!$object) {
    		$object = $this->getObject();
    	}
    	return $object->getOrAdd('departements');
    }
    
    private function getNoeudDroit($type, $object = null)
    {
    	if (!$object) {
    		$object = $this->getObject();
    	}
    	return $this->getNoeudInterpro($object)->getOrAdd('droits')->getOrAdd($type);
    }
    
    private function getNoeudLabel($object = null)
    {
    	if (!$object) {
    		$object = $this->getObject();
    	}
    	return $this->getNoeudInterpro($object)->getOrAdd('labels');
    }
    
    private function setLabel($code, $libelle) {
    	$labels = $this->getObject()->getDocument()->labels;
    	if (!$labels->exist($code)) {
    		$label = $labels->add($code, $libelle);
    	}
    }
    
    private function setDroit($code, $libelle) {
    	$droits = $this->getObject()->getDocument()->droits;
    	if (!$droits->exist($code)) {
    		$droit = $droits->add($code, $libelle);
    		if (preg_match('/^([^_]+)_/', $code, $m)) {
				if (!$droits->exist($m[1])) {
					$droit = $droits->add($m[1], null);
				}
			}
    	}
    }
    
    private function setNodeDroit($droit, $date, $taux, $code, $libelle) {
    	$droit->date = $date;
    	$droit->taux = $taux;
    	$droit->code = $code;
    	$droit->libelle = $libelle;
    }
            
    public function save($con = null) {
    	$object = parent::save($con);
    	$values = $this->getValues();

    	if ($object->hasDepartements()) {
    		$object->remove('departements');
    		$departements = $this->getNoeudDepartement($object);
    		foreach ($values['secteurs'] as $value) {
    			$departements->add(null, $value['departement']);
    		}
    	}
    	if ($object->hasDroit(ConfigurationDroits::DROIT_DOUANE)) {
    		$this->getNoeudInterpro($object)->droits->remove('douane');
    		foreach ($values['droit_douane'] as $value) {
    			//if ($value['taux'] > 0) {
    				$this->setDroit('DOUANE', 'Douane');
    				$date = $value['date'];
    				if ($date) {
    					$date = explode('/', $date);
    					$date = new DateTime($date[2].'-'.$date[1].'-'.$date[0]);
    				}
    				$this->setNodeDroit($this->getNoeudDroit('douane', $object)->add(), $date->format('c'), $value['taux'], 'DOUANE', 'Douane');
    			//}
    		}
        }
        if ($object->hasDroit(ConfigurationDroits::DROIT_CVO)) {
    		$this->getNoeudInterpro()->droits->remove('cvo');
    		foreach ($values['droit_cvo'] as $value) {
    			//if ($value['taux'] > 0) {
    				$this->setDroit('CVO', 'Cvo');
    				$date = $value['date'];
    				if ($date) {
    					$date = explode('/', $date);
    					$date = new DateTime($date[2].'-'.$date[1].'-'.$date[0]);
    				}
    				$this->setNodeDroit($this->getNoeudDroit('cvo', $object)->add(), $date->format('c'), $value['taux'], 'CVO', 'Cvo');
    			//}
    		}
    	}
    	if ($object->hasLabels()) {
    		$this->getNoeudInterpro($object)->remove('labels');
    		$labels = $this->getNoeudLabel($object);
    		foreach ($values['labels'] as $value) {
    			$this->setLabel($value['code'], $value['label']);
    			$labels->add(null, $value['code']);
    		}
    	}
    	$object->getDocument()->save();
    	return $object;    	
    }
}