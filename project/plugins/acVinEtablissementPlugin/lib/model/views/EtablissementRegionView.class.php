<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class EtablissementRegionView
 * @author mathurin
 */
class EtablissementRegionView extends acCouchdbView {

    const KEY_FAMILLE = 0;
    const KEY_STATUT = 1;
    const KEY_REGION = 2;
    const KEY_COMMUNE = 3;
    const KEY_CODE_POSTAL = 4;
    const KEY_CVI = 5;
    const KEY_NOM = 6;
    const KEY_IDENTIFIANT = 7;

    public static function getInstance() {
        return acCouchdbManager::getView('etablissement', 'region', 'Etablissement');
    }

    public function findAll() {
        return $this->client->limit(100)->getView($this->design, $this->view);
    }

    public function findByFamillesAndRegionsNonSuspendus($familles, $regions, $limit = 100) {
        $etablissements = array();
        foreach ($familles as $famille) {
            foreach ($regions as $region) {                
                $etablissements = array_merge($etablissements, $this->findByFamilleAndRegionNonSuspendu($famille, $region, $limit));
            }            
        }
        return $etablissements;
    }

    public function findByFamilleAndRegionNonSuspendu($famille, $region = null, $limit = 100) {
        $query = null;
        if ($region) {
            $query = $this->client->startkey(array($famille, EtablissementClient::STATUT_ACTIF, $region))
                    ->endkey(array($famille, EtablissementClient::STATUT_ACTIF, $region, array()));
        } else {
            $query = $this->client->startkey(array($famille, EtablissementClient::STATUT_ACTIF))
                    ->endkey(array($famille, EtablissementClient::STATUT_ACTIF, array()));
        }
        
        if ($limit == null) {
            return $query->getView($this->design, $this->view)->rows;
        }
        return $query->limit($limit)->getView($this->design, $this->view)->rows;
    }

}
