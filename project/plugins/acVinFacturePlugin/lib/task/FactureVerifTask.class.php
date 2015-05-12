<?php

class FactureVerifTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
			    new sfCommandArgument('factureid', null, sfCommandOption::PARAMETER_REQUIRED, 'Facture id'),
    ));

    $this->addOptions(array(
			    new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
			    new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
			    new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
			    new sfCommandOption('directory', null, sfCommandOption::PARAMETER_REQUIRED, 'Output directory', '.'),
      // add your own options here
    ));

    $this->namespace        = 'facture';
    $this->name             = 'verif';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [generatePDF|INFO] task does things.
Call it with:

  [php symfony generatePDF|INFO]
EOF;
  }
  
  protected function execute($arguments = array(), $options = array())
  {
    sfContext::createInstance($this->configuration);
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $facture = FactureClient::getInstance()->find($arguments['factureid'], acCouchdbClient::HYDRATE_JSON);

    foreach($facture->lignes as $groupe_libelle => $groupe) {
      foreach($groupe as $num_ligne => $ligne) {
        $volume_doc = null;
        foreach($ligne->origine_mouvements as $doc_id => $mouvs) {
          foreach($mouvs as $mouv_id) {
            $doc = acCouchdbManager::getClient()->find($doc_id, acCouchdbClient::HYDRATE_JSON);
            foreach($doc->mouvements as $identifiant => $doc_mouvs) {
              foreach($doc_mouvs as $doc_mouv_id => $doc_mouv) {
                if($doc_mouv_id != $mouv_id || !preg_match("/^".$facture->identifiant."/", $identifiant)) {
                  continue;
                }
                if(!$doc_mouv->facturable) {
                  continue;
                }
                $volume_doc += $doc_mouv->volume;
              }
            }
          }
        }
        $volume_doc = abs(round($volume_doc, 2));
        $volume_mouv = abs(round($ligne->volume, 2));

        if($volume_doc != $volume_mouv) {
          echo "ERREUR;$facture->_id;$groupe_libelle;$num_ligne;$volume_mouv;$volume_doc\n";
        }
      }
    }
  }
}
