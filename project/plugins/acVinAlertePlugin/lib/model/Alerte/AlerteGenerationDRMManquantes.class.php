<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class AlerteGenerationVracsAttenteOriginal
 * @author mathurin
 */
class AlerteGenerationDRMManquantes extends AlerteGenerationDRM {

    public function getTypeAlerte() {

        return AlerteClient::DRM_MANQUANTE;
    }

    public function creations($import = false) {
        $periodes = $this->getPeriodes($import);
        echo "periodes définies\n";
        $etablissements = $this->getEtablissementsByTypeDR(EtablissementClient::TYPE_DR_DRM);
        echo "etablissements définies\n";

        $i=0;
        foreach ($etablissements as $etablissement) {

            foreach ($periodes as $periode) {
              $i++;

              if($i > 200) {
                sleep(1);
                $i = 0;
              }
                $drm_id = DRMClient::getInstance()->buildId($etablissement->identifiant, $periode);
                if ($drm_id) {
                    echo $drm_id." traitement drm\n";
                    $drm = DRMClient::getInstance()->find($drm_id);
                    if ($drm) {
                        continue;
                    }
                    $alerte = $this->createOrFindByDRM($this->buildDRMManquante($etablissement, $periode));
                    $alerte->type_relance = $this->getTypeRelance();
                    if ($alerte->isNew() || $alerte->isFerme()) {
                        $alerte->open($this->getDate());
                        $alerte->save();
                        echo "NOUVELLE ALERTE CREEE " . $alerte->_id . "\n";
                    }
                }
            }
        }
    }

    public function updates() {
        $i=0;
        $campagneManager = new CampagneManager("08-01");
        $limit_campagne = $campagneManager->getCampagneByDate((date('Y') - 1) . date('m-d'));

        foreach ($this->getAlertesOpen() as $alerteView) {
            $i++;

            if($i > 200) {
              sleep(1);
              $i = 0;
            }
            $id_document = $alerteView->key[AlerteHistoryView::KEY_ID_DOCUMENT_ALERTE];
            if ($id_document) {
                $alerte = AlerteClient::getInstance()->find($alerteView->id);
                $drm = DRMClient::getInstance()->find($id_document);
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
    }

    protected function getPeriodes($import = false) {
        $campagnes = $this->getCampagnes($import);
        $present_periode = ConfigurationClient::getInstance()->buildPeriodeFromDate($this->getDate());

        $periode_debut = ConfigurationClient::getInstance()->getPeriodeDebut($campagnes[0]);

        $present_periode_date = substr($present_periode, 0, 4) . "-" . substr($present_periode, 4, 2) . "-01";

        //CONDITION d'ouverture des DRM à +3 mois
        $periode_fin = ConfigurationClient::getInstance()->buildPeriodeFromDate(Date::addDelaiToDate("-3 month", $present_periode_date));

        $periodes = array();

        while ($periode_debut <= $periode_fin) {
            $periodes[] = $periode_debut;

            $periode_debut = ConfigurationClient::getInstance()->getPeriodeSuivante($periode_debut);
        }

        return $periodes;
    }

    protected function getCampagnes($import = false) {

        $campagneManager = new CampagneManager("08-01");
        $current_campagne = $campagneManager->getCampagneByDate($this->getDate());

        $campagnes = array();
        if ($import) {
            while ($current_campagne != $campagneManager->getPrevious($this->getFirstCampagneForImport())) {
                $campagnes[] = $current_campagne;
                $current_campagne = $campagneManager->getPrevious($current_campagne);
            }
        } else {
            $campagnes[] = $current_campagne;
            $campagnes[] = $campagneManager->getPrevious($current_campagne);
        }
        return array_reverse($campagnes);
    }

    protected function buildDRMManquante($etablissement, $periode) {
        $id = DRMClient::getInstance()->buildId($etablissement->identifiant, $periode);
        $drm_manquante = new stdClass();

        $drm_manquante->identifiant = $etablissement->identifiant;
        $drm_manquante->periode = $periode;
        $drm_manquante->campagne = ConfigurationClient::getInstance()->buildCampagneByPeriode($periode);
        $drm_manquante->version = null;
        $drm_manquante->declarant = new stdClass();
        $drm_manquante->declarant->region = $etablissement->region;
        $drm_manquante->declarant->nom = $etablissement->nom;
        $drm_manquante->_id = $id;

        return $drm_manquante;
    }

    public function creationsByDocumentsIds(array $documents_id, $document_type) {

    }

    public function isInAlerte($document) {

    }

    public function updatesByDocumentsIds(array $documents_id, $document_type) {

    }

    public function getTypeRelance() {
        return RelanceClient::TYPE_RELANCE_DRM_MANQUANTE;
    }

    public function executeCreations($import = false) {
        $this->creations($import);
    }

    public function executeUpdates($import = false) {
        $this->updates($import);
    }

}
