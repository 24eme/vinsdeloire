<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class AlerteGenerationVracsAttenteOriginal
 * @author mathurin
 */
class AlerteGenerationDRAManquante extends AlerteGenerationDRM {

    public function getTypeAlerte() {

        return AlerteClient::DRA_MANQUANTE;
    }

    public function creations($import = false) {
        echo "campagnes définies\n";
        $campagne_periode_arr = $this->getPeriodesByCampagnes($import);
        if(!count($campagne_periode_arr)){
            echo "Aucune Alertes DRA Manquantes à ouvrir\n";
            return;
        }

        $etablissements = $this->getEtablissementsByTypeDR(EtablissementClient::TYPE_DR_DRA);
        echo "etablissements définies\n";
        $i=0;
        foreach ($etablissements as $etablissement) {

            foreach ($campagne_periode_arr as $campagne => $campagne_periode) {
              $i++;

              if($i > 200) {
                sleep(1);
                $i = 0;
              }

                $dra = $this->isDraInCampagneArray($etablissement->identifiant, $campagne_periode);
                if ($dra) {
                    continue;
                }
                $alerte = $this->createOrFindByDRM($this->buildDRAManquante($etablissement, $campagne));

                $alerte->type_relance = $this->getTypeRelance();
                if ($alerte->isNew() || $alerte->isFerme()) {
                    $alerte->open($this->getDate());
                    echo "NOUVELLE ALERTE CREEE " . $alerte->_id . "\n";
                    $alerte->updateStatut(AlerteClient::STATUT_A_RELANCER, AlerteClient::MESSAGE_AUTO_RELANCE, $this->getDate());
                    $alerte->save();
                    echo "L'ALERTE " . $alerte->_id . " passe au statut à relancer\n";
                    $alerte->save();
                }
            }
        }
    }

    public function updates() {
        $i=0;
        $campagneManager = new CampagneManager("08-01");
        $limit_campagne = $campagneManager->getCampagneByDate((date(Y) - 1) . date('m-d'));

        foreach ($this->getAlertesOpen() as $alerteView) {

            $i++;

            if($i > 200) {
              sleep(1);
              $i = 0;
            }
            $id_document = $alerteView->key[AlerteHistoryView::KEY_ID_DOCUMENT_ALERTE];

            $alerte = AlerteClient::getInstance()->find($alerteView->id);
            $dra = $this->findOneDRAForFirstDRM($id_document);
            $etablissement = EtablissementClient::getInstance()->find($alerte->identifiant);
            if ($drm || ($etablissement->exclusion_drm == EtablissementClient::EXCLUSION_DRM_OUI) ||
                ($etablissement->statut == EtablissementClient::STATUT_SUSPENDU) || $alerte->campagne < $limit_campagne) {
                // PASSAGE AU STATUT FERME
                $alerte->updateStatut(AlerteClient::STATUT_FERME, AlerteClient::MESSAGE_AUTO_FERME, $this->getDate());
                $alerte->save();
                echo "L'ALERTE " . $alerte->_id . " passe au statut fermé\n";
            } elseif ($alerte->isRelancable()) {
                // PASSAGE AU STATUT A_RELANCER
                $relance = Date::supEqual($this->getDate(), $alerte->date_relance);
                if ($relance) {
                    $alerte->updateStatut(AlerteClient::STATUT_A_RELANCER, AlerteClient::MESSAGE_AUTO_RELANCE, $this->getDate());
                    $alerte->save();
                    echo "L'ALERTE " . $alerte->_id . " passe au statut à relancer\n";
                } else {
                    echo "L'ALERTE " . $alerte->_id . " ne change pas de statut (sera relancée le " . $alerte->date_relance . ")\n";
                }
            } elseif ($alerte->isRelancableAR()) {
                // PASSAGE AU STATUT A_RELANCER_AR
                $today = date('Y-m-d');
                $relanceAr = Date::supEqual($today, $alerte->date_relance_ar);
                if ($relanceAr) {
                    $alerte->updateStatut(AlerteClient::STATUT_A_RELANCER_AR, AlerteClient::MESSAGE_AUTO_RELANCE_AR, $today);
                    $alerte->save();
                    echo "L'ALERTE " . $alerte->_id . " passe au statut à relancer ar\n";
                } else {
                    echo "L'ALERTE " . $alerte->_id . " ne change pas de statut (sera relancée AR le " . $alerte->date_relance_ar . ")\n";
                }
            } else {
                echo "L'ALERTE " . $alerte->_id . " ne change pas de statut\n";
            }
        }
    }


    protected function buildDRAManquante($etablissement, $campagne) {
        $periode = ConfigurationClient::getInstance()->getPeriodeDebut($campagne);
        $id = DRMClient::getInstance()->buildId($etablissement->identifiant, $periode);
        $dra_manquante = new stdClass();

        $dra_manquante->identifiant = $etablissement->identifiant;
        $dra_manquante->periode = $periode;
        $dra_manquante->campagne = $campagne;
        $dra_manquante->version = null;
        $dra_manquante->declarant = new stdClass();
        $dra_manquante->declarant->region = $etablissement->region;
        $dra_manquante->declarant->nom = $etablissement->nom;
        $dra_manquante->_id = $id;
        return $dra_manquante;
    }

    public function creationsByDocumentsIds(array $documents_id, $document_type) {

    }

    protected function getCampagnes($import = false) {

        $monthDay = substr($this->getDate(), 5, 2) . substr($this->getDate(), 8, 2);
        $campagneManager = new CampagneManager("08-01");

        if (!$import && $monthDay != "1001" && $monthDay != "1002") {
            return array();
        }
        $lastCampagne = $campagneManager->getPrevious($campagneManager->getCampagneByDate($this->getDate()));
        $campagnes = array();
        if ($import) {
            while ($lastCampagne != $campagneManager->getPrevious($this->getFirstCampagneForImport())) {
                $campagnes[] = $lastCampagne;
                $lastCampagne = $campagneManager->getPrevious($lastCampagne);
            }
        } else {
            $campagnes[] = $lastCampagne;
        }
        return array_reverse($campagnes);
    }

    public function isDraInCampagneArray($identifiant, $periodes_by_campagne) {
        foreach ($periodes_by_campagne as $periodes) {
            foreach ($periodes as $periode) {
                $idDrm = DRMClient::getInstance()->buildId($identifiant, $periode);
               echo $idDrm." traitement dra \n";
                $drm = DRMClient::getInstance()->find($idDrm, acCouchdbClient::HYDRATE_JSON);
                if ($drm) {
                    return true;
                }
            }
        }
        return false;
    }

    public function findOneDRAForFirstDRM($drm_id) {
        $result = array();
        preg_match('/^DRM-([0-9]{8})-([0-9]{4})([0-9]{2})/', $drm_id,$result);
        $identifiant = $result[1];
        $annee = $result[2];
        $mois = $result[3];
        for ($i = $mois; $i <= "12"; $i++) {
            $periode = $annee.sprintf("%02d",$i);
            $dra = DRMClient::getInstance()->find(DRMClient::getInstance()->buildId($identifiant, $periode));
            if($dra){
                return $dra;
            }
        }
        for ($i = "01"; $i <= "07"; $i++) {
            $periode = ($annee+1).sprintf("%02d",$i);
            $dra = DRMClient::getInstance()->find(DRMClient::getInstance()->buildId($identifiant, $periode));
            if($dra){
                return $dra;
            }
        }
        return false;
    }

    public function updatesByDocumentsIds(array $documents_id, $document_type) {

    }

    protected function getPeriodesByCampagnes($import = false) {
        $campagnes = $this->getCampagnes($import);
        $periodes_by_campagnes = array();
        foreach ($campagnes as $campagne) {
            $periodes_by_campagnes[$campagne] = $this->getPeriodesByCampagne($campagne);
        }
        return $periodes_by_campagnes;
    }

    protected function getPeriodesByCampagne($campagne) {
        $periodes_by_campagne = array();
        $periode_debut = ConfigurationClient::getInstance()->getPeriodeDebut($campagne);
        $periode_fin = ConfigurationClient::getInstance()->getPeriodeFin($campagne);
        while ($periode_debut <= $periode_fin) {
            $periodes_by_campagne[$campagne][] = $periode_debut;
            $periode_debut = ConfigurationClient::getInstance()->getPeriodeSuivante($periode_debut);
        }
        return $periodes_by_campagne;
    }

    public function isInAlerte($document) {

    }

    public function getTypeRelance() {
        return RelanceClient::TYPE_RELANCE_DRA_MANQUANTE;
    }

    public function executeCreations($import = false) {
        $this->creations($import);
    }

    public function executeUpdates() {
        $this->updates();
    }

}
