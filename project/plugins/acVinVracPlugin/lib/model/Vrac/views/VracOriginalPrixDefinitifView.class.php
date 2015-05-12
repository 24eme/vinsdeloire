<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class VracOriginalPrixDefinitifView
 * @author mathurin
 */
class VracOriginalPrixDefinitifView extends acCouchdbView {

    const KEY_ORIGINAL = 0;
    const KEY_PRIX_VARIABLE = 1;
    const KEY_DATE_SAISIE = 2;
    const KEY_PART_VARIABLE = 3;  
    const KEY_IDENTIFIANT = 4;    
    const KEY_NOM = 5;

    public static function getInstance() {
        return acCouchdbManager::getView('vrac', 'originalPrixDefinitif', 'Vrac');
    }

    public function findContatsByWaitForOriginal() {
         return $this->client->startkey(array(1))
                        ->endkey(array(1, array()))
                        ->getView($this->design, $this->view)->rows;
    }
    
    public function findContatsByWaitForPrixDefinitif($date) {
         $year = substr($date, 0,4);
         $startCampagne = ($year-1).'-08-01';
         $endCampagne = $year.'-07-31';
         $result0 = $this->client->startkey(array(1,1,$startCampagne,null))
                        ->endkey(array(1,1,$endCampagne,null,array()))
                        ->getView($this->design, $this->view)->rows;
         $result1 = $this->client->startkey(array(0,1,$startCampagne,null))
                        ->endkey(array(0,1,$endCampagne,null,array()))
                        ->getView($this->design, $this->view)->rows;
        return array_merge($result0, $result1);
    }

}

