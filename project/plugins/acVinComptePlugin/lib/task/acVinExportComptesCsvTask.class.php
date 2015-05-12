<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :	
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acVinComptePlugin task.
 * 
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinExportComptesCsvTask extends sfBaseTask 
{

    protected function configure() 
    {
        $this->addArguments(array(
            new sfCommandArgument('tiers_types', sfCommandArgument::IS_ARRAY, 'Type du tiers : Recoltant|MetteurEnMarche|Acheteur', array("Recoltant", "MetteurEnMarche", "Acheteur")),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'comptes-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony maintenanceExportTiersGammaTask|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) 
    {
        ini_set('memory_limit', '512M');
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $tiers = array();
        foreach ($arguments['tiers_types'] as $tiers_type) {
            $tiers = array_merge($tiers, acCouchdbManager::getClient($tiers_type)->getAll(acCouchdbClient::HYDRATE_JSON)->getDocs());
        }

        $comptes = array();
        foreach ($tiers as $t) {
            if (count($t->compte) == 0) {
                $this->logSection($t->cvi, "COMPTE VIDE", null, 'ERROR');
                continue;
            }
            foreach ($t->compte as $id_compte) {
                $comptes[$id_compte][] = $t;
            }
        }

        $csv = new ExportCsv(array(
                    "type" => "Type",
                    "login" => "Login",
                    "statut" => "Statut",
                    "mot_de_passe" => "Code de création",
                    "email" => "Email",
                    "cvi" => "Numéro CVI",
                    "civaba" => "Numéro CIVABA",
                    "siret" => "Numéro Siret",
                    "qualite" => "Qualité",
                    "civilite" => "Civilité",
                    "nom" => "Nom Prénom",
                    "adresse" => "Adresse",
                    "code postal" => "Code postal",
                    "commune" => "Commune",
                    "exploitant_sexe" => "Sexe de l'exploitant",
                    "exploitant_nom" => "Nom de l'exploitant"
                ));

        $validation = array(
            "type" => array("required" => true, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "statut" => array("required" => true, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "email" => array("required" => false, "type" => "string"),
            "cvi" => array("required" => false, "type" => "string"),
            "civaba" => array("required" => false, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "qualite" => array("required" => false, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => false, "type" => "string"),
            "code postal" => array("required" => false, "type" => "string"),
            "commune" => array("required" => false, "type" => "string"),
            "exploitant_sexe" => array("required" => false, "type" => "string"),
            "exploitant_nom" => array("required" => false, "type" => "string")
        );

        foreach ($comptes as $id_compte => $tiers_c) {
            $compte = acCouchdbManager::getClient()->retrieveDocumentById($id_compte, acCouchdbClient::HYDRATE_JSON);
            if ($compte) {
                foreach ($tiers_c as $t) {

                    $intitule = $t->intitule;
                    $nom = $t->nom;
                    $adresse = $t->siege;
                    if (!$adresse->adresse && isset($t->exploitant)) {
                        if ($t->exploitant->nom) {
                            $intitule = $t->exploitant->sexe;
                            $nom = $t->exploitant->nom;
                        }
                        $adresse = $t->exploitant;
                    }
                    
                    $email = $compte->email;
                    if (!$email) {
                        $email = $t->email;
                    }

                    if (substr($compte->mot_de_passe, 0, 6) == "{TEXT}") {
                        $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compte->mot_de_passe);
                    } else {
                        $mot_de_passe = "Compte déjà créé";
                    }

                    try {
                        $csv->add(array(
                            "type" => $t->type,
                            "login" => $compte->login,
                            "statut" => $compte->statut,
                            "mot_de_passe" => $mot_de_passe,
                            "email" => $email,
                            "cvi" => $this->getTiersField($t, 'cvi', true),
                            "civaba" => $this->getTiersField($t, 'civaba'),
                            "siret" => $this->getTiersField($t, 'siret'),
                            "qualite" => $this->getTiersField($t, 'qualite'),
                            "civilite" => $intitule,
                            "nom" => $nom,
                            "adresse" => $adresse->adresse,
                            "code postal" => $adresse->code_postal,
                            "commune" => $adresse->commune,
                            "sexe de l'exploitant" => $t->exploitant->sexe,
                            "nom de l'exploitant" => $t->exploitant->nom,
                                ), $validation);
                    } catch (Exception $exc) {
                        $this->logSection($t->cvi, $exc->getMessage(), null, 'ERROR');
                    }
                }
            } else {
                $this->logSection($t->cvi, "COMPTE INEXISTANT", null, 'ERROR');
            }
        }

        echo $csv->output(false);
    }

    protected function getTiersField($tiers, $field, $default = null) {
        $value = $default;
        if (isset($tiers->{$field})) {
            $value = $tiers->{$field};
        }
        return $value;
    }
}