<?php
class DSStocksView extends acCouchdbView
{
    const KEY_CAMPAGNE = 0;
    const KEY_SOCIETE_IDENTIFIANT = 1;
    const KEY_ETABLISSEMENT_IDENTIFIANT = 2;
    const KEY_PRODUIT_HASH = 3;
    const KEY_PERIODE = 4;
    const KEY_IDENTIFIANT = 5;

    const VALUE_VOLUME = 0;
    const VALUE_VOLUME_EN_ELABORATION = 1;
    const VALUE_VOLUME_VCI = 2;
    const VALUE_VOLUME_RESERVE_QUALITATIVE = 3;
    const VALUE_DECLARANT_NOM = 4;
    const VALUE_PRODUIT_LIBELLE = 5;

    public static function getInstance() {

        return acCouchdbManager::getView('ds', 'stocks', 'DS');
    }

    public function findByCampagneAndEtablissement($campagne, $societe_identifiant, $etablissement_identifiant) {    

        return $this->builds(
                            $this->client->startkey(array($campagne, $societe_identifiant, $etablissement_identifiant))
                            ->endkey(array($campagne, $societe_identifiant, $etablissement_identifiant, array()))
                            ->getView($this->design, $this->view)->rows
                            );
    }

    public function builds($rows) {
        $dss = array();

        foreach($rows as $row) {
            $key = $row->key[self::KEY_ETABLISSEMENT_IDENTIFIANT] . '_' . $row->key[self::KEY_PRODUIT_HASH];
            $dss[$key] = $this->build($row);
        }

        return $dss;
    }

    public function build($row) {
        $ds = new stdClass();
        $ds->societe_identifiant = $row->key[self::KEY_SOCIETE_IDENTIFIANT];
        $ds->etablissement_identifiant = $row->key[self::KEY_ETABLISSEMENT_IDENTIFIANT];
        $ds->declarant = new stdClass();
        $ds->declarant->nom = $row->value[self::VALUE_DECLARANT_NOM];
        $ds->campagne = $row->key[self::KEY_CAMPAGNE];
        $ds->periode = $row->key[self::KEY_PERIODE];
        $ds->produit_hash = $row->key[self::KEY_PRODUIT_HASH];
        $ds->produit_libelle = $row->value[self::VALUE_PRODUIT_LIBELLE];
        $ds->volume = $row->value[self::VALUE_VOLUME];

        return $ds;
    }
}  