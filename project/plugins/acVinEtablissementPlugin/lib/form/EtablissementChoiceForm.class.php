<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EtablissementChoiceForm extends baseForm {
	
	protected $interpro_id;
	
  	public function __construct($interpro_id, $defaults = array(), $options = array(), $CSRFSecret = null)
  	{
  		$this->interpro_id = $interpro_id;
    	parent::__construct($defaults, $options, $CSRFSecret);
  	}

    public function configure()
    {
        $this->setWidget('identifiant', new WidgetEtablissement(array('interpro_id' => $this->interpro_id)));

        $this->widgetSchema->setLabel('identifiant', 'Sélectionner un établissement&nbsp;:');
        
        $this->setValidator('identifiant', new ValidatorEtablissement(array('required' => true)));
        
        $this->validatorSchema['identifiant']->setMessage('required', 'Le choix d\'un etablissement est obligatoire');        
        
        $this->widgetSchema->setNameFormat('etablissement[%s]');
    }

    public function configureFamilles($familles) {
        $this->getWidget('identifiant')->setOption('familles', $familles);
        $this->getValidator('identifiant')->setOption('familles', $familles);
    }

    public function getEtablissement() {

        return $this->getValidator('identifiant')->getDocument();
    }
    
}

