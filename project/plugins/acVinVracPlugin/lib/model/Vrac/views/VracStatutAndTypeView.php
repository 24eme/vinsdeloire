<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracStatutAndTypeView
 * @author mathurin
 */
class VracStatutAndTypeView extends acCouchdbView {

    const KEY_STATUT = 0;
    const KEY_TYPE = 1;
    const KEY_DATE_SAISIE = 2;
    const KEY_IDENTIFIANT = 3;    
    const KEY_NOM = 4;

    public static function getInstance() {
        return acCouchdbManager::getView('vrac', 'statutAndType', 'Vrac');
    }

    public function findContatsByStatutsAndTypes($statuts, $types) {
        $result = array();
        foreach ($statuts as $statut) {
            foreach ($types as $type) {
                $result = array_merge($result, $this->findContatsByStatutAndType($statut, $type, '0000-00-00'));
            }
        }
        return $result;
    }
    
    public function findContatsByStatutsAndTypesAndDate($statuts, $types, $date_saisie) {
        $result = array();
        foreach ($statuts as $statut) {
            foreach ($types as $type) {
                $result = array_merge($result, $this->findContatsByStatutAndType($statut, $type, $date_saisie));
            }
        }
        return $result;
    }

    public function findContatsByStatutAndType($statut, $type, $date_saisie) {

        return $this->client->startkey(array($statut, $type, '0000-00-00'))
                        ->endkey(array($statut, $type, $date_saisie, array()))
                        ->getView($this->design, $this->view)->rows;
    }
    
    public function findContatsByStatut($statut) {

        return $this->client->startkey(array($statut))
                        ->endkey(array($statut, array()))
                        ->getView($this->design, $this->view)->rows;
    }
    
    public function findContatsByStatutsAndTypesAndDates($statuts, $types, $date_debut,$date_fin) {
        $result = array();
        foreach ($statuts as $statut) {
            foreach ($types as $type) {
                $result = array_merge($result, $this->client->startkey(array($statut, $type, $date_debut))
                        ->endkey(array($statut, $type, $date_fin, array()))
                        ->getView($this->design, $this->view)->rows);
            }
        }
        return $result;
    }       

}

