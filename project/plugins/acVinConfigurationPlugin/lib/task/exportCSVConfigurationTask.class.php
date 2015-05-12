<?php

class exportCSVConfigurationTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'export';
    $this->name             = 'csv-configuration';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCSVConfiguration|INFO] task does things.
Call it with:

  [php symfony exportCSVConfiguration|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $produits = ConfigurationClient::getCurrent()->getProduits();

    echo sprintf("hash;libelle;code douane;code produit;code comptable;cvo\n");

    foreach($produits as $hash => $produit) {
        try {
            $droit_cvo = $produit->getDroitCVO(date('Y-m-d'))->taux;
        } catch(Exception $e) {
            $droit_cvo = null;
        }
        echo sprintf("%s;%s;%s;%s;%s;%s\n", $hash, $produit->getLibelleFormat(), $produit->getCodeDouane(), $produit->getCodeProduit(), $produit->getCodeComptable(),$droit_cvo);
    }
  }
}
