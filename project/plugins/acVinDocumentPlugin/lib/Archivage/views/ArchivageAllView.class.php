<?php

class ArchivageAllView extends acCouchdbView
{
    
    const KEYS_TYPE = 0;
    const KEYS_CAMPAGNE = 1;
    const KEYS_NUMERO_ARCHIVE = 2;

    public static function getInstance() {

        return acCouchdbManager::getView('archivage', 'all');
    }
    
    public function getLastNumeroArchiveByTypeAndCampagne($type, $campagne, $fourchette_basse = 0, $fourchette_haute = 99999, $format = "%05d") {  

        $rows = $this->getViewByTypeAndCampagne($type, $campagne, $fourchette_basse, $fourchette_haute, $format)
                     ->reduce(true)
                     ->group_level(self::KEYS_CAMPAGNE+1)      
                     ->getView($this->design, $this->view)->rows;

        $nb_docs = 0;

        foreach($rows as $row) {
            $nb_docs = $row->value;
        }

        if($nb_docs == 0) {

            return $fourchette_basse;
        }

        $rows = $this->getViewByTypeAndCampagne($type, $campagne, $fourchette_basse, $fourchette_haute, $format)
                     ->reduce(false)
                     ->skip($nb_docs - 1)
                     ->limit(1)
                     ->getView($this->design, $this->view)->rows;

        foreach($rows as $row) {

            return $row->key[self::KEYS_NUMERO_ARCHIVE];
        }

        return $this->getLastNumeroArchiveByTypeAndCampagne($type, $campagne, $fourchette_basse, $fourchette_haute, $format);
    }

    protected function getViewByTypeAndCampagne($type, $campagne, $fourchette_basse = 0, $fourchette_haute = 99999, $format) {

            return $this->client
                        ->startkey(array($type, $campagne, sprintf($format, $fourchette_basse)))
                        ->endkey(array($type, $campagne, sprintf($format, $fourchette_haute), array()));
    }
    
}  