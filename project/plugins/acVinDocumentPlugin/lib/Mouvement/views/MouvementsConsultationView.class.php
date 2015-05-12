<?php

class MouvementsConsultationView extends acCouchdbView
{
    const KEY_TYPE = 0;
    const KEY_ETABLISSEMENT_IDENTIFIANT = 1;
    const KEY_CAMPAGNE = 2;
    const KEY_PERIODE = 3;
    const KEY_ID = 4;
    const KEY_PRODUIT_HASH = 5;
    const KEY_TYPE_HASH = 6;
    const KEY_VRAC_NUMERO = 7;
    const KEY_DETAIL_IDENTIFIANT = 8;

    const VALUE_ETABLISSEMENT_NOM = 0;
    const VALUE_PRODUIT_LIBELLE = 1;
    const VALUE_TYPE_LIBELLE = 2;
    const VALUE_VOLUME = 3;
    const VALUE_VRAC_DESTINATAIRE = 4;
    const VALUE_DETAIL_LIBELLE = 5;
    const VALUE_DATE_VERSION = 6;
    const VALUE_VERSION = 7;
    const VALUE_CVO = 8;
    const VALUE_FACTURABLE = 9;
    const VALUE_MOUVEMENT_ID = 10;

    public static $types_document = array("DRM", "SV12");


    public static function getInstance() {

        return acCouchdbManager::getView('mouvement', 'consultation');
    }

    public function getByIdentifiantAndCampagne($id_or_identifiant, $campagne) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);
        $mouvements = array();
        foreach(self::$types_document as $type) {
            $mouvements = array_merge($mouvements, $this->buildMouvements($this->findByTypeEtablissementAndCampagne($type, $identifiant, $campagne)->rows));
        }

        ksort($mouvements);

        return $mouvements;
    }

    public function findByTypeAndEtablissement($type, $id_or_identifiant) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);

        return $this->client->startkey(array($type, $identifiant))
                            ->endkey(array($type, $identifiant, array()))
                            ->getView($this->design, $this->view);
    }

    public function findByTypeEtablissementAndCampagne($type, $id_or_identifiant, $campagne) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);
        return $this->client->startkey(array($type, $identifiant, $campagne))
                            ->endkey(array($type, $identifiant, $campagne, array()))
                            ->getView($this->design, $this->view);
    }

    public function findByTypeEtablissementAndPeriode($type, $id_or_identifiant, $campagne, $periode) {
        $identifiant = EtablissementClient::getInstance()->getIdentifiant($id_or_identifiant);

        return $this->client->startkey(array($type, $identifiant, $campagne, $periode))
                            ->endkey(array($type, $identifiant, $campagne, $periode, array()))
                            ->getView($this->design, $this->view);
    }

    protected function buildMouvements($rows) {
        $mouvements = array();
        foreach($rows as $row) {
            $mouvement = $this->buildMouvement($row);
            $mouvement_sort = sprintf('%02d', str_replace('M', '', $mouvement->version)*1);
            $mouvements[$mouvement->date_version.$mouvement->type.$mouvement_sort.$mouvement->doc_id.$mouvement->id] = $mouvement;
        }

        return $mouvements;
    }

    protected function buildMouvement($row) {
        $mouvement = new stdClass();
        $mouvement->type = $row->key[self::KEY_TYPE];
        $mouvement->doc_id = $row->key[self::KEY_ID];
        $mouvement->type_hash = $row->key[self::KEY_TYPE_HASH];
        $mouvement->etablissement_nom = $row->value[self::VALUE_ETABLISSEMENT_NOM];
        $mouvement->produit_hash = $row->key[self::KEY_PRODUIT_HASH];
        $mouvement->produit_libelle = $row->value[self::VALUE_PRODUIT_LIBELLE];
        $mouvement->type_libelle = $row->value[self::VALUE_TYPE_LIBELLE];
        $mouvement->volume = $row->value[self::VALUE_VOLUME];
        $mouvement->detail_identifiant = $row->key[self::KEY_DETAIL_IDENTIFIANT];
        $mouvement->detail_libelle = $row->value[self::VALUE_DETAIL_LIBELLE];        
        $mouvement->date_version =  $row->value[self::VALUE_DATE_VERSION];
        $mouvement->version = $row->value[self::VALUE_VERSION];
        $mouvement->vrac_numero =  $row->key[self::KEY_VRAC_NUMERO];
        $mouvement->vrac_destinataire =  $row->value[self::VALUE_VRAC_DESTINATAIRE];
        $mouvement->cvo =  $row->value[self::VALUE_CVO];
        $mouvement->facturable =  $row->value[self::VALUE_FACTURABLE];
        $mouvement->id = $row->value[self::VALUE_MOUVEMENT_ID];
	if ($mouvement->vrac_numero) {
	  $mouvement->numero_archive = $row->value[self::VALUE_TYPE_LIBELLE];
	  if (strlen($mouvement->numero_archive) != 5) {
	    $vrac = VracClient::getInstance()->find('VRAC-'.$mouvement->vrac_numero);
	    $mouvement->numero_archive = $vrac->numero_archive;
	  }
	}
        return $mouvement;
    }

    public function getWords($mouvements) {
        $words = array();
        
        foreach($mouvements as $mouvement) {
            $words[] = $this->getWord($mouvement);
        }

        return $words;
    }

    protected function getWord($mouvement) {

    }

}  