<?php

class DRMCalendrier {

    private $etablissements = null;
    protected $etablissement = null;
    protected $campagne = null;
    protected $periodes = null;
    protected $drms = null;
    protected $isTeledeclarationMode = false;
    protected $multiEtbs;
    protected $transmises = null;
    protected $coherentes = null;

    const VIEW_INDEX_ETABLISSEMENT = 0;
    const VIEW_CAMPAGNE = 1;
    const VIEW_PERIODE = 2;
    const VIEW_VERSION = 3;
    const VIEW_MODE_SAISIE = 4;
    const VIEW_STATUT = 5;
    const VIEW_STATUT_DOUANE_ENVOI = 6;
    const VIEW_STATUT_DOUANE_ACCUSE = 7;
    const VIEW_NUMERO_ARCHIVAGE = 8;
    const STATUT_EN_COURS_NON_TELEDECLARE = 'STATUT_EN_COURS_NON_TELEDECLARE';
    const STATUT_NOUVELLE = 'NOUVELLE';
    const STATUT_NOUVELLE_BLOQUEE = 'NOUVELLE BLOQUÉE';
    const STATUT_EN_COURS = 'EN_COURS';
    const STATUT_VALIDEE = 'VALIDEE';
    const STATUT_VALIDEE_NON_TELEDECLARE = 'STATUT_VALIDEE_NON_TELEDECLARE';
    const STATUT_CLOTURE = 'EN_COURS';

    public function __construct($etablissement, $campagne, $isTeledeclarationMode = false) {
        $this->etablissement = $etablissement;
        $this->campagne = $campagne;
        $this->isTeledeclarationMode = $isTeledeclarationMode;
        $this->periodes = $this->buildPeriodes();
        $this->etablissements = array();
        foreach ($this->etablissement->getSociete()->getEtablissementsObj(!$this->isTeledeclarationMode) as $e) {
            $this->etablissements[] = $e->etablissement;
        }

        $this->multiEtbs = ((count($this->etablissements) > 1) && $this->isTeledeclarationMode);

        $this->loadDRMs();
        $this->loadStatuts();
    }

    protected function buildPeriodes() {

        if ($this->campagne == -1) {
            return DRMClient::getInstance()->getLastMonthPeriodes(12);
        }
        $periodes = array();
        $current = date('Ym');
        foreach (DRMClient::getInstance()->getPeriodes($this->campagne) as $p) {
            if ($current >= $p)
                $periodes[] = $p;
        }
        return $periodes;
    }

    protected function loadDRMs() {
        $this->drms = array();
        foreach ($this->etablissements as $etablissement) {
            $etbIdentifiant = $etablissement->identifiant;
            if (!array_key_exists($etbIdentifiant, $this->drms)) {
                $this->drms[$etbIdentifiant] = array();
            }
            foreach ($this->periodes as $periode) {
                $drm = DRMClient::getInstance()->viewMasterByIdentifiantPeriode($etbIdentifiant, $periode);
                $this->drms[$etbIdentifiant][$periode] = $drm;
                if (!isset($drm[10])) {
                    $drm[10] = null;
                    $drm[12] = null;
                }
                $this->transmises[$etbIdentifiant][$periode] = ($drm[10] == 'SUCCESS');
                if (!isset($drm[12])) {
                    $drm[12] = null;
                }
                $this->coherentes[$etbIdentifiant][$periode] = $drm[12];
            }
        }
    }

    public function getIdentifiant() {

        return $this->etablissement->identifiant;
    }

    public function getEtablissement() {

        return $this->etablissement;
    }

    public function getPeriodeVersion($periode, $etablissement = false) {
        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        if (!$this->hasDRM($periode, $etablissement)) {

            return;
        }

        $drm = $this->drms[$etablissement->identifiant][$periode];

        return DRMClient::getInstance()->buildPeriodeAndVersion($drm[self::VIEW_PERIODE], $drm[self::VIEW_VERSION]);
    }

    public function getPeriodes() {

        return $this->periodes;
    }

    public function hasDRM($periode, $etablissement = false) {
        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }

        if (!isset($this->drms[$etablissement->identifiant]))
            return false;

        if (!isset($this->drms[$etablissement->identifiant][$periode]))
            return false;

        return ($this->drms[$etablissement->identifiant][$periode]);
    }

    public function getId($periode) {
        if (!$this->hasDRM($periode)) {

            return;
        }

        $drm = $this->drms[$this->etablissement->identifiant][$periode];

        return DRMClient::getInstance()->buildId($drm[self::VIEW_INDEX_ETABLISSEMENT], $drm[self::VIEW_PERIODE], $drm[self::VIEW_VERSION]);
    }

    public function getPeriodeLibelle($periode) {
        return ConfigurationClient::getInstance()->getPeriodeLibelle($periode);
    }

    public function getMoisLibelle($periode) {
        return ConfigurationClient::getInstance()->getMoisLibelle($periode);
    }

    public function getNumero($periode) {

        return $this->getPeriodeVersion($periode);
    }

    public function getStatut($periode, $etablissement = false) {

        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        return (isset($this->statuts[$etablissement->identifiant][$periode]))? $this->statuts[$etablissement->identifiant][$periode] : null;
    }

    public function getTransmise($periode, $etablissement = false) {

        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        return $this->transmises[$etablissement->identifiant][$periode];
    }

    public function getCoherente($periode, $etablissement = false) {

        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        return $this->coherentes[$etablissement->identifiant][$periode];
    }



    private function loadStatuts() {
        $this->statuts = array();
        $lastPeriode = Date::addDelaiToDate('-36 month', null, 'Ym');
        foreach ($this->etablissements as $etablissement) {
            $etbIdentifiant = $etablissement->identifiant;
            if (!array_key_exists($etbIdentifiant, $this->statuts)) {
                $this->statuts[$etbIdentifiant] = array();
            }
            $hasteledeclaree = false;
            $periodes = $this->periodes;
            sort($periodes);
            $has_en_cours = false;
            foreach ($periodes as $periode) {
                $statut = $this->computeStatut($periode, $etablissement);
                if (($statut === self::STATUT_VALIDEE) || ($statut === self::STATUT_EN_COURS)) {
                    $hasteledeclaree = true;
                }
                if ((($statut == self::STATUT_EN_COURS) &&  $hasteledeclaree) || ($statut == self::STATUT_EN_COURS_NON_TELEDECLARE)) {
                  $has_en_cours = true;
                }
                if ($this->isTeledeclarationMode) {
                    $drm = $this->drms[$etbIdentifiant][$periode];
                    if (!$hasteledeclaree) {
                        $statut = self::STATUT_VALIDEE_NON_TELEDECLARE;
                        if ($this->isTeledeclarationMode && $this->computeStatut($periode, $etablissement) === self::STATUT_NOUVELLE
                        && (($periode >= $lastPeriode) || ($etablissement->type_dr == EtablissementClient::TYPE_DR_DRA && preg_match('/07$/', $periode)) ) ) {
                            $statut = self::STATUT_NOUVELLE;
                        }
                    }
                    if ($statut == self::STATUT_EN_COURS &&  !$hasteledeclaree) {
                      $statut = self::STATUT_VALIDEE_NON_TELEDECLARE;
                    }
                    if ($statut == self::STATUT_NOUVELLE && $has_en_cours) {
                      $statut = self::STATUT_NOUVELLE_BLOQUEE;
                    }
                    if ($statut == self::STATUT_NOUVELLE && ($etablissement->type_dr == EtablissementClient::TYPE_DR_DRA) && !preg_match('/07$/', $periode)) {
                      $statut = self::STATUT_NOUVELLE_BLOQUEE;
                    }
                }
                $this->statuts[$etbIdentifiant][$periode] = $statut;
            }
            if ($has_en_cours) {
                    foreach($periodes as $periode => $statuts) {
                        if ($this->statuts[$etbIdentifiant][$periode] ==  self::STATUT_NOUVELLE) {
                            $this->statuts[$etbIdentifiant][$periode] = self::STATUT_NOUVELLE_BLOQUEE;
                        }
                    }
            }
        }
    }

    public function getStatutsForIdentifiantPeriode($identifiant, $periode) {
        if (!isset($this->statuts) || !$this->statuts) {
            $this->loadStatuts();
        }
        return $this->statuts[$identifiant][$periode];
    }

    private function computeStatut($periode, $etablissement) {

        if (!$this->hasDRM($periode, $etablissement)) {
            return self::STATUT_NOUVELLE;
        }

        $drm = $this->drms[$etablissement->identifiant][$periode];

        if ($drm[self::VIEW_STATUT]) {
            $drm = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etablissement->identifiant, $periode);
            if (!$drm->isTeledeclare()) {
                return self::STATUT_VALIDEE_NON_TELEDECLARE;
            }
            return self::STATUT_VALIDEE;
        }
        $drm = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etablissement->identifiant, $periode);
        if (!$drm->isTeledeclare()) {
            return self::STATUT_EN_COURS_NON_TELEDECLARE;
        }
        return self::STATUT_EN_COURS;
    }

    public function getStatutForAllEtablissements($periode) {
        $statuts = array();
        foreach ($this->etablissements as $etablissement) {
            $statuts[] = $this->getStatut($periode, $etablissement);
        }
        $nouvelle = false;
        $validee = false;
        foreach ($statuts as $statut) {
            if ($statut === self::STATUT_EN_COURS) {
                return $statut;
            }
            if ($statut === self::STATUT_NOUVELLE) {
                $nouvelle = true;
            }
            if ($statut === self::STATUT_VALIDEE) {
                $validee = true;
            }
        }
        if ($nouvelle)
            return self::STATUT_NOUVELLE;
        if ($validee)
            return self::STATUT_VALIDEE;

        return self::STATUT_VALIDEE_NON_TELEDECLARE;
    }

    public function isTeledeclare($periode, $etablissement = false) {
        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        return DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etablissement->identifiant, $periode)->isTeledeclare();
    }

    public function getNumeroArchive($periode, $etablissement = false) {
        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        if (!$this->hasDRM($periode, $etablissement)) {
            return;
        }
        $drm = $this->drms[$etablissement->identifiant][$periode];
        return $drm[self::VIEW_NUMERO_ARCHIVAGE];
    }

    public function getDRM($periode, $etablissement = null) {
        if (!$etablissement) {
            $etablissement = $this->etablissement;
        }
        if (!$this->hasDRM($periode, $etablissement))
            return null;
        return DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etablissement->identifiant, $periode);
    }

    public function getLastDrmToCompleteAndToStart() {
        $drmLastWithStatut = array();
        foreach ($this->etablissements as $etb) {
            if (!array_key_exists($etb->identifiant, $drmLastWithStatut)) {
                $drmLastWithStatut[$etb->identifiant] = new stdClass();
                $drmLastWithStatut[$etb->identifiant]->nom = $etb->nom;
                $drmLastWithStatut[$etb->identifiant]->statut = self::STATUT_VALIDEE;
                $drmLastWithStatut[$etb->identifiant]->periode = null;
            }
            foreach ($this->getPeriodes() as $periode) {
                $statut = $this->getStatut($periode, $etb);
                if ($statut == self::STATUT_EN_COURS) {
                    $drm = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etb->identifiant, $periode);
                    $drmLastWithStatut[$etb->identifiant]->drm = $drm;
                    if ($drm->isTeledeclare()) {
                        $drmLastWithStatut[$etb->identifiant]->statut = self::STATUT_EN_COURS;
                    } else {
                        $drmLastWithStatut[$etb->identifiant]->statut = self::STATUT_EN_COURS_NON_TELEDECLARE;
                    }
                    break;
                }
                if ($statut == self::STATUT_NOUVELLE) {
                    $drmLastWithStatut[$etb->identifiant]->statut = self::STATUT_NOUVELLE;
                }
                if ($statut == self::STATUT_VALIDEE_NON_TELEDECLARE || $statut == self::STATUT_VALIDEE) {
                  break;
                }
                $drmLastWithStatut[$etb->identifiant]->periode = $periode;
                $drmLastWithStatut[$etb->identifiant]->drm = DRMClient::getInstance()->findMasterByIdentifiantAndPeriode($etb->identifiant, $periode);
            }
        }
        return $drmLastWithStatut;
    }

    public function getDrmsToCreateArray() {
        $drmsToCreate = array();
        foreach ($this->getPeriodes() as $periode) {
            foreach ($this->etablissements as $etb) {
                if ($this->getStatut($periode, $etb) == self::STATUT_NOUVELLE) {
                    if (!array_key_exists($etb->identifiant, $drmsToCreate)) {
                        $drmsToCreate[$etb->identifiant] = array();
                    }
                    $drmsToCreate[$etb->identifiant][$periode] = true;
                }
            }
        }
        return $drmsToCreate;
    }

    public function isMultiEtablissement() {
        return $this->multiEtbs;
    }

    public function getEtablissements() {
        return $this->etablissements;
    }

}
