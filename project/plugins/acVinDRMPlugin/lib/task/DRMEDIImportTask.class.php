<?php

class DRMEDIImportTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
            new sfCommandArgument('periode', sfCommandArgument::REQUIRED, "Periode de la DRM"),
            new sfCommandArgument('identifiants', sfCommandArgument::IS_ARRAY, "Identifiant de l'établissement (identifiant, cvi ou n° accises")
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('date-validation', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', false),
            new sfCommandOption('facture', null, sfCommandOption::PARAMETER_REQUIRED, 'Flag automatiquement les mouvements a facturé', false),
            new sfCommandOption('savewarning', null, sfCommandOption::PARAMETER_REQUIRED, 'Sauver les DRM en statut warning', false),
            new sfCommandOption('check', null, sfCommandOption::PARAMETER_REQUIRED, "Check only (no real import)", false),
        ));

        $this->namespace        = 'drm';
        $this->name             = 'edi-import';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
    The [importVrac|INFO] task does things.
    Call it with:

      [php symfony import:drm|INFO]
EOF;

    }

    protected function execute($arguments = array(), $options = array())
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $etablissement = null;

        foreach($arguments['identifiants'] as $i) {
            if(!$i) {
                continue;
            }
            $etablissement = EtablissementClient::getInstance()->find($i, acCouchdbClient::HYDRATE_JSON);

            if($etablissement) {
                break;
            }

            $etablissement = EtablissementClient::getInstance()->findByNoAccise($i);

            if($etablissement) {
                break;
            }

            $etablissement = EtablissementClient::getInstance()->findByCvi($i);

            if($etablissement) {
                break;
            }
        }

        if(!$etablissement) {
            echo "L'établissement n'existe pas;".implode(",", $arguments['identifiants'])."\n";
            return;
        }

        $identifiant = $etablissement->identifiant;

        if(DRMClient::getInstance()->find('DRM-'.$identifiant.'-'.$arguments['periode'], acCouchdbClient::HYDRATE_JSON)) {
            echo "Existe : ".'DRM-'.$identifiant.'-'.$arguments['periode']."\n";
            return;
        }

        if (!preg_match('/^[0-9]{6}$/', $arguments['periode'])) {
            throw new sfException('invalid periode : '.$arguments['periode']);
        }
        $drm = DRMClient::getInstance()->createDocByPeriode($identifiant, $arguments['periode'], true);

       try {
        $drmCsvEdi = new DRMImportCsvEdiStandalone($arguments['file'], $drm, true);
        $drmCsvEdi->checkCSV();
        $condition_save = ($drmCsvEdi->getCsvDoc()->getStatut() != "VALIDE");
        if($options['savewarning']){
          $condition_save = $condition_save && ($drmCsvEdi->getCsvDoc()->getStatut() != "WARNING");
        }

        if($condition_save) {
            $csv = $drmCsvEdi->getCsv();
            foreach($drmCsvEdi->getCsvDoc()->erreurs as $erreur) {
                echo sprintf("%s : %s;#%s\n", $erreur->diagnostic, $erreur->csv_erreur, implode(";", $csv[$erreur->num_ligne-1]));
            }
            return;
        }

        if ($options['check']) {
          return ;
        }

        $drmCsvEdi->importCSV();


        if($condition_save) {
            $csv = $drmCsvEdi->getCsv();
            foreach($drmCsvEdi->getCsvDoc()->erreurs as $erreur) {
                echo sprintf("%s : %s;#%s\n", $erreur->diagnostic, $erreur->csv_erreur, implode(";", $csv[$erreur->num_ligne-1]));
            }
        }

        $drm->validate();

            if($options['date-validation']) {
                $drm->valide->date_saisie = $options['date-validation'];
                $drm->valide->date_signee = $options['date-validation'];
            }

            if($options['facture']) {
                $drm->facturerMouvements();
            }

            $drm->type_creation = "IMPORT";

            $drm->save();

            DRMClient::getInstance()->generateVersionCascade($drm);

        } catch(Exception $e) {
            echo $e->getMessage().";#".$arguments['periode'].";".$identifiant."\n";
            if(isset($options['trace'])) {
                throw $e;
            }
            return;
        }

        echo "Création : ".$drm->_id."\n";
    }

}
