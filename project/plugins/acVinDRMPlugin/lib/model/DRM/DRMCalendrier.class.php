<?php

class DRMCalendrier {

    protected $identifiant = null;
    protected $campagne = null;
    protected $periodes = null;
    protected $drms = null;

    const VIEW_INDEX_ETABLISSEMENT = 0;
    const VIEW_CAMPAGNE = 1;
    const VIEW_PERIODE = 2;
    const VIEW_VERSION = 3;
    const VIEW_MODE_SAISIE = 4;
    const VIEW_STATUT = 5;
    const VIEW_STATUT_DOUANE_ENVOI = 6;
    const VIEW_STATUT_DOUANE_ACCUSE = 7;
    const VIEW_NUMERO_ARCHIVAGE = 8;
    const STATUT_NOUVELLE = 'NOUVELLE';
    const STATUT_EN_COURS = 'EN_COURS';
    const STATUT_VALIDEE = 'VALIDEE';
    const STATUT_CLOTURE = 'EN_COURS';

    public function __construct($identifiant, $campagne) {
        $this->identifiant = $identifiant;
        $this->campagne = $campagne;
        $this->periodes = DRMClient::getInstance()->getPeriodes($this->campagne);

        $this->loadDRMs();
    }

    protected function loadDRMs() {
        $this->drms = array();

        $drms = DRMClient::getInstance()->viewByIdentifiantAndCampagne($this->identifiant, $this->campagne);

        foreach ($drms as $drm) {
            if (array_key_exists($drm[self::VIEW_PERIODE], $this->drms)) {

                continue;
            }

            $this->drms[$drm[self::VIEW_PERIODE]] = $drm;
        }
    }

    public function getIdentifiant() {

        return $this->identifiant;
    }

    public function getPeriodeVersion($periode) {
        if (!$this->hasDRM($periode)) {

            return;
        }

        $drm = $this->drms[$periode];

        return DRMClient::getInstance()->buildPeriodeAndVersion($drm[self::VIEW_PERIODE], $drm[self::VIEW_VERSION]);
    }

    public function getPeriodes() {

        return $this->periodes;
    }

    public function hasDRM($periode) {

        return isset($this->drms[$periode]);
    }

    public function getId($periode) {
        if (!$this->hasDRM($periode)) {

            return;
        }

        $drm = $this->drms[$periode];

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

    public function getStatut($periode) {
        if (!$this->hasDRM($periode)) {

            return self::STATUT_NOUVELLE;
        }

        $drm = $this->drms[$periode];

        if ($drm[self::VIEW_STATUT]) {

            return self::STATUT_VALIDEE;
        }

        return self::STATUT_EN_COURS;
    }

    public function getNumeroArchivage($periode) {
        if (!$this->hasDRM($periode)) {
            return;
        }
        $drm = $this->drms[$periode];
        return $drm[self::VIEW_NUMERO_ARCHIVAGE];
    }

    public function getStatutLibelle($periode) {
        
    }

    public function getDRM($periode) {
        $id = $this->getId($periode);

        if (!$id) {

            return null;
        }

        return DRMClient::getInstance()->find($id);
    }

}

