<?php

/**
 * Model for ConfigurationCepage
 *
 */
class ConfigurationCepage extends BaseConfigurationCepage {

    const TYPE_NOEUD = 'cepage';

    public function getChildrenNode() {

        return null;
    }

    public function getAppellation() {

        return $this->getCouleur()->getLieu()->getAppellation();
    }

    public function getCertification() {

        return $this->getAppellation()->getCertification();
    }

    public function getGenre() {

        return $this->getAppellation()->getGenre();
    }

    public function getLieu() {

        return $this->getCouleur()->getLieu();
    }

    public function getMention() {

        return $this->getLieu()->getMention();
    }

    public function getCepage() {

        return $this;
    }

    public function getProduitsAll($interpro = null, $departement = null) {

        return array($this->getHash() => $this);
    }

    public function compressDroits() {
        $this->compressDroitsSelf();
    }

    public function getCouleur() {
        return $this->getParentNode();
    }

    public static function isCodeDouaneNeedTav($code_douane) {
        if(!$code_douane){

            return false;
        }

        return preg_match('/(RHUM_|ALCOOL_|SPIRITUEUX_|AUTRES_ALCOOLS|PREMIX)/', $code_douane);
    }

    public static function isCodeDouanePI($code_douane) {
        if(!$code_douane){

            return false;
        }

        return preg_match('/_PI_/', $code_douane);
    }

    public function needTav() {

        return self::isCodeDouaneNeedTav($this->code_douane);
    }

    public function setDonneesCsv($datas) {
        parent::setDonneesCsv($datas);

        $this->getCouleur()->setDonneesCsv($datas);
        $this->libelle = ($datas[ProduitCsvFile::CSV_PRODUIT_CEPAGE_LIBELLE]) ? $datas[ProduitCsvFile::CSV_PRODUIT_CEPAGE_LIBELLE] : null;
        $this->code = $this->formatCodeFromCsv($datas[ProduitCsvFile::CSV_PRODUIT_CEPAGE_CODE]);

        $this->setDroitDouaneCsv($datas, ProduitCsvFile::CSV_PRODUIT_CEPAGE_CODE_APPLICATIF_DROIT);
        $this->setDroitCvoCsv($datas, ProduitCsvFile::CSV_PRODUIT_CEPAGE_CODE_APPLICATIF_DROIT);
    }

    public function getCorrespondanceHash() {

        return $this->getDocument()->getCorrespondanceHash($this->getHash());
    }

    public function getTypeNoeud() {

        return self::TYPE_NOEUD;
    }

    public function addInterpro($interpro)
    {

        return $this->getParentNode()->addInterpro($interpro);
    }

    public function hasDroits() {
        return true;
    }

    public function hasCodes() {

        return true;
    }

}
