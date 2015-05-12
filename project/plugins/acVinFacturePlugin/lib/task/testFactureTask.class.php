<?php

class testFactureTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'test';
    $this->name             = 'facture';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [testFacture|INFO] task does things.
Call it with:

    [php symfony test:Facture|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $facture = FactureClient::getInstance()->find('FACTURE-110001-2012020101');
    if($facture) {
      $facture->delete();
    }

    // add your code here
    $facture = new Facture();
    $facture->_id = 'FACTURE-110001-2012020101';
    $facture->numero_facture = '2012020101';
    $facture->date_emission = '2012-02-01';
    $facture->campagne = '2011-2012';
    $facture->emetteur->adresse = "Chateau InterLoire";
    $facture->emetteur->code_postal = '44120';
    $facture->emetteur->ville = 'Balieue Nantaise';
    $facture->emetteur->service_facturation = 'Neilly';
    $facture->emetteur->telephone = '0212321232';
    $facture->client_identifiant = 'ETABLISSEMENT-110001';
    $facture->identifiant = '110001';
    $facture->client->raison_sociale = "Garage d'actualys";
    $facture->client->adresse = "1 rue garnier";
    $facture->client->code_postal = "92100";
    $facture->client->ville = "Neuilly sur seine";
    $facture->add("lignes")->add(0, array('origine_type' => 'DRM', 'origine_identifiant' => 'DRM-123-2012-01', 'origine_date' => '2012-01', 'produit_type' => 'Vin', 'produit_libelle' => 'Anjou rouge', 'produit_hash' => 'AOC/.../TOU', 'mouvement_type' => FactureClient::FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE, 'contrat_identifiant' => '', 'contrat_libelle' => '', 'echeance_code' => 'A', 'volume' => 10, 'cotisation_taux' => 4.5, 'montant_ht' => 45));
    $facture->add("lignes")->add(1, array('origine_type' => 'DRM', 'origine_identifiant' => 'DRM-123-2012-01', 'origine_date' => '2012-01', 'produit_type' => 'Vrac', 'produit_libelle' => 'Anjou rouge', 'produit_hash' => 'AOC/.../TOU', 'mouvement_type' => FactureClient::FACTURE_LIGNE_MOUVEMENT_TYPE_CONTRAT, 'contrat_identifiant' => 'VRAC-123-2012-01-12', 'contrat_libelle' => 'contrat avec bidule', 'echeance_code' => 'B', 'volume' => 10, 'cotisation_taux' => 5.5, 'montant_ht' => 55));
    $facture->add("echeances")->add(0, array('echeance_code' => 'A', 'echeance_date' => '2012-03-31', 'montant_ttc' => 53.82));
    $facture->add("echeances")->add(1, array('echeance_code' => 'B', 'echeance_date' => '2012-03-31', 'montant_ttc' => 32.89));
    $facture->add("echeances")->add(2, array('echeance_code' => 'B', 'echeance_date' => '2012-05-31', 'montant_ttc' => 32.89));

    $facture->total_ht = 100;
    $facture->total_ttc = 119.6;
    $facture->origines = array('DRM-123-2012-01' => 'DRM de janvier', 'DRM-123-2011-12' => 'DRM de décembre');
    $facture->save();
  }
}
