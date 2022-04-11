<?php

/**
 * Model for Alerte
 *
 */
class Alerte extends BaseAlerte {

    protected $config = null;

    public function __construct() {
        parent::__construct();
        if (!($this->isNew())) {
            $this->config = new AlerteConfig($this->type_alerte);
        }
    }

    public function getConfig() {
        if (!$this->config)
            $this->config = new AlerteConfig($this->type_alerte);
        return $this->config;
    }

    public function setTypeAlerte($value) {
        $this->_set('type_alerte', $value);
        $this->config = new AlerteConfig($this->type_alerte);
    }

    protected function constructId() {
        $this->_id = AlerteClient::getInstance()->buildId($this->type_alerte, $this->id_document);
    }

    public function getDocumentObject($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return acCouchdbManager::getClient()->find($this->id_document, $hydrate);
    }

    public function setCreationDate($creation_date) {
        $this->date_creation = $creation_date;
    }

    public function buildFirstDateRelance() {
        switch ($this->getTypeAlerte()) {
            case AlerteClient::DRM_MANQUANTE :
                $this->date_relance = Date::addDelaiToDate("+1 month", $this->getDateCreation());
                break;

            default:
                break;
        }
    }

    public function isRelancable() {
        return $this->isStatutNouveau();
    }

    public function isRelancableAR() {
        return $this->isStatutEnAttenteReponse();
    }

    public function open($date = null) {
        $this->updateStatut(AlerteClient::STATUT_NOUVEAU, 'Nouvelle alerte générée', $date);
    }

    public function updateStatut($statut, $commentaire = null, $date = null) {
        if (is_null($date)) {
            $date = date('Y-m-d');
        }
        $this->statuts->add(null, array('statut' => $statut, 'commentaire' => $commentaire, 'date' => $date));
        $this->add('date_dernier_statut', $date);
        if ($statut == AlerteClient::STATUT_A_RELANCER_AR) {
            if (substr($this->type_relance, strlen($this->type_relance) - 3) != "_AR") {
                $this->type_relance.="_AR";
            }
        } else {
            $this->type_relance = str_replace("_AR", "", $this->type_relance);
        }
    }

    public function getStatut() {
        return $this->statuts->getLast();
    }

    public function isOpen() {
        return !$this->isFinished();
    }

    public function isStatutNouveau() {
        return $this->getStatut()->statut == AlerteClient::STATUT_NOUVEAU;
    }

    public function isStatutEnAttenteReponse() {
        return $this->getStatut()->statut == AlerteClient::STATUT_EN_ATTENTE_REPONSE;
    }

    public function isEnSommeil() {
        return $this->getStatut()->statut == AlerteClient::STATUT_EN_SOMMEIL;
    }

    public function isFerme() {
        return $this->getStatut()->statut == AlerteClient::STATUT_FERME;
    }

    public function isModifiable() {
        return !$this->isFerme() && !$this->isEnSommeil();
    }

    public function getLibelle() {
        return AlerteClient::$alertes_libelles[$this->getTypeAlerte()] . ' (' . $this->libelle_document . ')';
    }

    public function save() {
        if ($statut = $this->getStatut()) {
            $this->statut_courant = $statut->statut;
        }
        return parent::save();
    }

}
