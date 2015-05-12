<?php

class SV12UpdateAddProduitForm extends acCouchdbForm 
{
  protected $_sv12 = null;
  protected $_config = null;
  protected $_choices_produits;
  
  public function __construct(SV12 $sv12, $options = array(), $CSRFSecret = null) 
  {
    $this->_sv12 = $sv12;
    $this->_config = $this->getConfig();
    $defaults = array('withviti' => 'withviti');
    parent::__construct($sv12, $defaults, $options, $CSRFSecret);
  }
  
    public function configure() 
    {
      $this->setWidgets(array(
			      'hashref' => new sfWidgetFormChoice(array('choices' => $this->getChoices())),
			      'raisinetmout' => new sfWidgetFormChoice(array('choices' => array(VracClient::TYPE_TRANSACTION_RAISINS => 'Raisins', VracClient::TYPE_TRANSACTION_MOUTS => 'Moûts'), 'expanded'=>true)),
			      'identifiant' => new  WidgetEtablissement(array('interpro_id' => 'INTERPRO-inter-loire', 'familles' => array(EtablissementFamilles::FAMILLE_PRODUCTEUR))),
			      'withviti' => new  sfWidgetFormInputCheckbox(array('value_attribute_value' => 'withviti'))
			      ));
      
      $this->widgetSchema->setLabels(array(
					   'hashref' => 'Produit&nbsp;: ', 
					   'raisinetmout' => 'Raisins et moûts&nbsp;:', 
					   'identifiant' => 'Viticulteur&nbsp;:', 
					   'withviti' => "Affecter l'enlevement à un viti"
					   ));

      $this->setValidators(array(
				 'hashref'  => new sfValidatorChoice(array('required' => true,  'choices' => array_keys($this->getProduits())),array('required' => "Aucun produit n'a été saisi !")),
				 'raisinetmout' => new sfValidatorChoice(array('choices' => array(VracClient::TYPE_TRANSACTION_RAISINS, VracClient::TYPE_TRANSACTION_MOUTS), 'required' => false)),
				 'identifiant' => new ValidatorEtablissement(array('required' => false)),
				 'withviti' => new sfValidatorChoice(array('required' => false, 'choices' => array('withviti')))
				 ));

      $this->validatorSchema->setPostValidator(new SV12AddProduitValidator());
      $this->widgetSchema->setNameFormat('sv12_add_produit[%s]');
    }

    public function getChoices() 
    {
        if (is_null($this->_choices_produits)) {
	  $this->_choices_produits = array_merge(array("" => ""),
						 array("nouveau" => $this->getProduits()));
        }
        return $this->_choices_produits;
    }

    public function getProduits() 
    {
        $date = $this->_sv12->getFirstDayOfPeriode();
        return $this->_config->formatProduits($date);
    }

    public function addProduit() 
    {
      if (!$this->isValid()) {
	throw $this->getErrorSchema();
      }
      if (!isset($this->values['withviti']) || !$this->values['withviti']) {
	$sv12Contrat = $this->_sv12->contrats->add(SV12Client::SV12_KEY_SANSVITI.str_replace('/', '-', $this->values['hashref']));
	$sv12Contrat->updateNoContrat($this->getConfig()->get($this->values['hashref']));
	return $sv12Contrat;
      }

      $etablissement = EtablissementClient::getInstance()->find($this->values['identifiant']);

      $sv12Contrat = $this->_sv12->contrats->add(SV12Client::SV12_KEY_SANSCONTRAT.'-'.$etablissement->identifiant.'-'.$this->values['raisinetmout'].str_replace('/', '-', $this->values['hashref']));
      echo "update no contrat avec viti\n";
      $sv12Contrat->updateNoContrat($this->getConfig()->get($this->values['hashref']), array('vendeur_identifiant' => $etablissement->identifiant, 'vendeur_nom' => $etablissement->nom, 'contrat_type' => $this->values['raisinetmout']));
      
    }
    
    public function getConfig() 
    {
        return ConfigurationClient::getCurrent();
    }
}