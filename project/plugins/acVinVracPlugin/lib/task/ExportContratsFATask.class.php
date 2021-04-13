<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MaintenanceTransfertChaiTask
 *
 * @author mathurin
 */
class ExportContratsFATask extends sfBaseTask {

    const CSV_FA_NUM_LIGNE = 0;
    const CSV_FA_TYPE_CONTRAT = 1;
    const CSV_FA_CAMPAGNE = 2;
    const CSV_FA_NUM_ARCHIVAGE = 3;
    const CSV_FA_CODE_LIEU_VISA = 4;
    const CSV_FA_CODE_ACTION = 5;
    const CSV_FA_DATE_CONTRAT = 6;
    const CSV_FA_DATE_VISA = 7;
    const CSV_FA_CODE_COMMUNE_LIEU_VINIFICATION = 8;
    const CSV_FA_INDICATION_DOUBLE_FIN = 9;
    const CSV_FA_CODE_INSEE_DEPT_COMMUNE_ACHETEUR = 10;
    const CSV_FA_NATURE_ACHETEUR = 11;
    const CSV_FA_SIRET_ACHETEUR = 12;
    const CSV_FA_CVI_VENDEUR = 13;
    const CSV_FA_NATURE_VENDEUR = 14;
    const CSV_FA_SIRET_VENDEUR = 15;
    const CSV_FA_COURTIER = 16; // (O/N)
    const CSV_FA_DELAI_RETIRAISON = 17;
    const CSV_FA_POURCENTAGE_ACCOMPTE = 18;
    const CSV_FA_DELAI_PAIEMENT = 19;
    const CSV_FA_CODE_TYPE_PRODUIT = 20;
    const CSV_FA_CODE_DENOMINATION_VIN_IGP = 21;
    const CSV_FA_PRIMEUR = 22;
    const CSV_FA_BIO = 23;
    const CSV_FA_COULEUR = 24;
    const CSV_FA_ANNEE_RECOLTE = 25;
    const CSV_FA_CODE_ELABORATION = 26; // (O/N)
    const CSV_FA_VOLUME = 27;
    const CSV_FA_DEGRE = 28; //(Degré vin si type de contrat = V (vins) Degré en puissance si type de contrat = M (moût))
    const CSV_FA_PRIX = 29;
    const CSV_FA_UNITE_PRIX = 30; // H pour Hl
    const CSV_FA_CODE_CEPAGE = 31;
    const CSV_FA_CODE_DEST = 32; // Z

    protected $produitsConfiguration = null;

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'vinsdeloire'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('dryrun', null, sfCommandOption::PARAMETER_REQUIRED, 'Mode de test ne sauvgarde pas en base', false),
                // add your own options here
        ));

        $this->namespace = 'export';
        $this->name = 'contrats-france-agrimer';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [export-contrats-france-agrimer|INFO] task update contrat from chai src to chai dst.
Call it with:

  [php symfony export:contrats-france-agrimer|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $this->produitsConfiguration = ConfigurationClient::getCurrent()->getProduits();

        $contrats = $this->getContrats();
        $this->printCSV($contrats->rows, $options['dryrun']);
    }

    protected function getContrats() {

        return VracClient::getInstance()->retrieveAllVracs();
    }

    protected function printCSV($contratsView, $dryrun = false) {
        if (!count($contratsView)) {
            fwrite(STDERR, "Aucun contrats\n");
        }
        $cpt = 0;
        fwrite(STDERR, "#NUM_LIGNE;TYPE_CONTRAT;CAMPAGNE;NUM_ARCHIVAGE;CODE_LIEU_VISA;CODE_ACTION;DATE_CONTRAT;DATE_VISA;CODE_COMMUNE_LIEU_VINIFICATION;INDICATION_DOUBLE_FIN;CODE_INSEE_DEPT_COMMUNE_ACHETEUR;NATURE_ACHETEUR;SIRET_ACHETEUR;CVI_VENDEUR;NATURE_VENDEUR;SIRET_VENDEUR;COURTIER;DELAI_RETIRAISON;POURCENTAGE_ACCOMPTE;DELAI_PAIEMENT;CODE_TYPE_PRODUIT;CODE_DENOMINATION_VIN_IGP;PRIMEUR;BIO;COULEUR;ANNEE_RECOLTE;CODE_ELABORATION;VOLUME;DEGRE;PRIX;UNITE_PRIX;CODE_CEPAGE;CODE_DEST;ID_DOC;\n");
        fwrite(STDERR, "#num_ligne;type_contrat;campagne;num_archive;code_lieu_visa;code_action;date_contrat;date_visa;code_commune_lieu_vinification;indicateur_double_fin;code_insee_dept_commune_acheteur;nature_acheteur;siret_acheteur;cvi_vendeur;nature_vendeur;siret_vendeur;courtier (O/N);delai_retiraison;pourcentage_accompte;delai_paiement;code_type_produit;code_denomination_vin_IGP;primeur;bio;couleur;annee_recolte;code_elaboration (O/N);volume;degre (Degré vin si type de contrat = V (vins) Degré en puissance si type de contrat = M (moût));prix;unité_prix (H);code_cepage;code_dest (Z)\n");
        foreach ($contratsView as $contratView) {
            if (!preg_match('/\/[a-z]+\/[a-z]+\/IGP(.*)/', $contratView->value[VracHistoryView::VALUE_PRODUIT])) {
                continue;
            }
            if ($contratView->value[VracHistoryView::VALUE_STATUT] == VracClient::STATUS_CONTRAT_ATTENTE_SIGNATURE ||
                $contratView->value[VracHistoryView::VALUE_STATUT] == VracClient::STATUS_CONTRAT_BROUILLON
               ) {
                continue;
            }
            $contrat = VracClient::getInstance()->find($contratView->id);

            if (!$this->isContratATransmettre($contrat)) {
                continue;
            }

            $cpt++;
            $ligne = array();

            $produit = $this->produitsConfiguration[$contratView->value[VracHistoryView::VALUE_PRODUIT]];

            $acheteur = $contrat->getAcheteurObject();
            $acheteurCompte = $acheteur->getMasterCompte();
            $acheteurSociete = $acheteur->getSociete();

            $vendeur = $contrat->getVendeurObject();
            $vendeurCompte = $vendeur->getMasterCompte();
            $vendeurSociete = $vendeur->getSociete();

            $ligne[self::CSV_FA_NUM_LIGNE] = "01"; // ? ou $cpt;
            $type_contrat = "";
            if ($contrat->type_transaction == VracClient::TYPE_TRANSACTION_VIN_VRAC || $contrat->type_transaction == VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE) {
                $type_contrat = "V";
            }
            if ($contrat->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS || $contrat->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS) {
                $type_contrat = "M";
                continue;
            }
            $ligne[self::CSV_FA_TYPE_CONTRAT] = $type_contrat; // V pour vrac, M pour Mout
            $ligne[self::CSV_FA_CAMPAGNE] = (substr($contrat->millesime, 0, 4) && substr($contrat->campagne, 0, 4) > substr($contrat->millesime, 0, 4)) ? substr($contrat->millesime, 0, 4) : substr($contrat->campagne, 0, 4);
            $ligne[self::CSV_FA_NUM_ARCHIVAGE] = '0'.$contrat->numero_archive; // Est-ce notre numéro d'archivage?
            $ligne[self::CSV_FA_CODE_LIEU_VISA] = "020"; //Interloire
            $ligne[self::CSV_FA_CODE_ACTION] = ($contrat->exist("versement_fa")) ? $contrat->versement_fa : 'NC'; // NC = Nouveau Contrat, SC = Supprimé Contrat, MC = Modifié Contrat
            $ligne[self::CSV_FA_DATE_CONTRAT] = Date::francizeDate($contrat->date_signature);
            $ligne[self::CSV_FA_DATE_VISA] = Date::francizeDate($contrat->date_signature);

            $ligne[self::CSV_FA_CODE_COMMUNE_LIEU_VINIFICATION] = $vendeur->getCodeInsee(); // Code Insee Vendeur
            $ligne[self::CSV_FA_INDICATION_DOUBLE_FIN] = 'N'; // Quelle signification?
            /**
             * ACHETEUR
             */
            $ligne[self::CSV_FA_CODE_INSEE_DEPT_COMMUNE_ACHETEUR] = $acheteur->getCodeInsee(); // Code Insee Acheteur
            $ligne[self::CSV_FA_NATURE_ACHETEUR] = ($acheteur->exist('nature_inao'))? $acheteur->nature_inao : '08';
            $ligne[self::CSV_FA_SIRET_ACHETEUR] = preg_replace('/ /', '', $acheteurSociete->siret);
            /**
             * VENDEUR
             */
            $ligne[self::CSV_FA_CVI_VENDEUR] = $vendeur->cvi;
            $ligne[self::CSV_FA_NATURE_VENDEUR] = ($vendeur->exist('nature_inao'))? $vendeur->nature_inao : '01';
            $ligne[self::CSV_FA_SIRET_VENDEUR] = preg_replace('/ /', '', $vendeurSociete->siret);
            /**
             * COURTIER
             */
            $ligne[self::CSV_FA_COURTIER] = ($contrat->mandataire_exist) ? 'O' : 'N';

            $ligne[self::CSV_FA_DELAI_RETIRAISON] = 'X';
            $ligne[self::CSV_FA_POURCENTAGE_ACCOMPTE] = 'X'; //DISABLED sprintf("%d", $contrat->acompte);
            $ligne[self::CSV_FA_DELAI_PAIEMENT] = 'X'; // DISABLED sprintf("%0.1f", $this->getDelaiPaiement($contrat));

            $ligne[self::CSV_FA_CODE_TYPE_PRODUIT] = ($type_contrat == 'M') ? 'E' : "PA";
            $ligne[self::CSV_FA_CODE_DENOMINATION_VIN_IGP] = $this->getCodeDenomVinIGP($produit); // ASSIGNER LES CODE PRODUITS IGP
            $ligne[self::CSV_FA_PRIMEUR] = ($produit->getMention()->getKey() == "PM") ? "O" : "N";
            $ligne[self::CSV_FA_BIO] = ($contrat->isBio()) ? "O" : "N";
            $ligne[self::CSV_FA_COULEUR] = $this->getCouleurIGP($contrat, $produit);
            $ligne[self::CSV_FA_ANNEE_RECOLTE] = (substr($contrat->millesime, 0, 4))? substr($contrat->millesime, 0, 4) : substr($contrat->campagne, 0, 4); //??
            $ligne[self::CSV_FA_CODE_ELABORATION] = 'P'; //DISABLED ($contrat->conditionnement_crd == 'NEGOCE_ACHEMINE') ? "P" : "N";
            $ligne[self::CSV_FA_VOLUME] = $this->sprintFloatFr($contrat->volume_propose);
            $ligne[self::CSV_FA_DEGRE] = "12.5"; //DISABLED sprintf("%0.1f", $contrat->degre);
            $ligne[self::CSV_FA_PRIX] = $this->sprintFloatFr($contrat->prix_initial_unitaire_hl);
            $ligne[self::CSV_FA_UNITE_PRIX] = 'H';
            $ligne[self::CSV_FA_CODE_CEPAGE] = $this->getCodeCepageFA($contrat, $produit);
            $ligne[self::CSV_FA_CODE_DEST] = ($type_contrat == 'M') ? 'E' : "Z";
            $ligne[self::CSV_FA_CODE_DEST + 1 ] = $contrat->_id;
            /*
              Comment connaitre?
              Z  = Consommation
              M = Vin destiné à l'élaboration de mousseux
              V  = Vinaigre
              O  = Apéritif à base de vin ou vermouth
              Pour les moûts :
              P = Vinification pour agrément en VDP (obsolète)
              T = Vinification pour agrément en VDT (obsolète)
              R = Enrichissement, édulcoration
              E = Elaboration de jus de raisin
              C = concentration
              A = Autres destinations
              X = Imprécis
             */

             //export pour FA
            for($i = 0 ;  $i < count($ligne) - 1 ; $i++) {
                echo '"' . $ligne[$i] . '";';
            }
            echo "\n";
            //export pour le debug (contenant l'identifiant du doc)
            for($i = 0 ;  $i < count($ligne) ; $i++) {
                fwrite(STDERR, '"' . $ligne[$i] . '";');
            }
            fwrite(STDERR, "\n");
            $contrat->add('versement_fa', VracClient::VERSEMENT_FA_TRANSMIS);

            if($dryrun) {

                continue;
            }

            $contrat->save();
        }
    }

    public function diffDate($date1, $date2) {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $diffMois = $datetime1->diff($datetime2)->m;
        return $diffMois;
    }

    protected function getDelaiPaiement($contrat) {
        switch ($contrat->delai_paiement) {
            case "60_JOURS": {
                    return 2.0;
                }
            case "30_JOURS": {
                    return 1.0;
                }
            case "90_JOURS": {
                    return 3.0;
                }
            case "COMPTANT": {
                    return 0.0;
                }
            case "ACCORD_INTERPROFESSIONNEL": {
                    return 2.5;
                }
            case "45_JOURS": {
                    return 1.5;
                }
        }
    }

    protected function getCodeDenomVinIGP($produit) {
        return sprintf('%03d',preg_replace('/3[BSR]([0-9][0-9][0-9]).*/' , '\1', $produit->getCodeDouane()));
    }

    protected function getCouleurIGP($contrat, $produit) {
        $couleur = $produit->getCouleur()->getKey();
        if ($contrat->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS) {
            switch ($couleur) {
                case "blanc_sec":
                case "blanc_doux":
                case "blanc":
                    return "BL";

                default:
                    return "CO";
            }
        }
        switch ($couleur) {
            case "blanc_sec":
            case "blanc":
            case "blanc_doux":
                return "BL";
            case "rouge":
                return "RG";
            case "rose":
                return "RS";
        }
        return $couleur;
    }

    public function isContratATransmettre($contrat) {
        if (!$contrat->exist('versement_fa')) {
            return false;
        }
        if (($contrat->versement_fa == VracClient::VERSEMENT_FA_ANNULATION) || ($contrat->versement_fa == VracClient::VERSEMENT_FA_MODIFICATION) || ($contrat->versement_fa == VracClient::VERSEMENT_FA_NOUVEAU)) {
            return true;
        }
        return false;
    }

    protected function getCodeCepageFA($contrat, $produit) {
        switch (strtoupper($produit->getLibelleFormat('', '%ce%'))) {
            case "ABOURIOU":
                return '1011';
            case "CABERNET FRANC":
                return '1101';
            case "CABERNET SAUVIGNON":
                return '1111';
            case "CHARDONNAY":
                return '1141';
            case "CHENIN":
                return '1161';
            case "COT":
                return '1321';
            case "FOLLE BLANCHE":
                return '1201';
            case "GAMAY DE CHAUDENAY NOIR":
                return '1235';
            case "GAMAY NOIR":
                return '1221';
            case "GROLLEAU GRIS":
                return '1263';
            case "GROLLEAU NOIR":
                return '1261';
            case "MELON":
                return '1441';
            case "MERLOT":
                if ($this->getCouleurIGP($contrat, $produit) == 'RG') {
                    return '1373';
                }else{
                    return '1371';
                }
            case "ORBOIS":
                return '2007';
            case "PINEAU D'AUNIS":
                return '1491';
            case "PINOT BLANC":
                return '1503';
            case "PINOT GRIS":
                return '1507';
            case "PINOT NOIR":
                return '1511';
            case "SAUVIGNON BLANC":
                return '1561';
            case "SAUVIGNON GRIS":
                return '1567';
            default:
                return '';

        }
    }

    private function sprintFloatFr($f) {
        return sprintf($f, '0.4f');
    }

}
