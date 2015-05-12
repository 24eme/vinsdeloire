<?php
class DRMStocksView extends acCouchdbView
{
    const KEY_CAMPAGNE = 0;
    const KEY_SOCIETE_IDENTIFIANT = 1;
    const KEY_ETABLISSEMENT_IDENTIFIANT = 2;
    const KEY_PRODUIT_HASH = 3;
    const KEY_PERIODE = 4;
    const KEY_VERSION = 5;

    const VALUE_VOLUME_STOCK_DEBUT_MOIS = 0;
    const VALUE_VOLUME_ENTREES = 1;
    const VALUE_VOLUME_RECOLTE = 2;
    const VALUE_VOLUME_SORTIES = 3;
    const VALUE_VOLUME_FACTURABLE = 4;
    const VALUE_VOLUME_STOCK_FIN_MOIS = 5;
    const VALUE_DECLARANT_NOM = 6;
    const VALUE_PRODUIT_LIBELLE = 7;

    public static function getInstance() {

        return acCouchdbManager::getView('drm', 'stocks', 'DRM');
    }

    public function getStockFin($campagne, Etablissement $etablissement, $hash_produit) {
        $drms = $this->findByCampagneAndEtablissementAndProduit($campagne, null, $etablissement->identifiant, $hash_produit);
        $stock_fin = 0;
        foreach($drms as $drm) {
            $stock_fin = $drm->volume_stock_fin_mois;
        }

        return $stock_fin;
    }

    public function findByCampagneAndEtablissementAndProduit($campagne, $societe_identifiant, $etablissement_identifiant, $hash_produit) {    

        return $this->builds(
                            $this->client->startkey(array($campagne, $societe_identifiant, $etablissement_identifiant, $hash_produit))
                            ->endkey(array($campagne, $societe_identifiant, $etablissement_identifiant, $hash_produit, array()))
                            ->getView($this->design, $this->view)->rows
                            );
    }

    public function findByCampagneAndEtablissement($campagne, $societe_identifiant, $etablissement_identifiant) {    

        return $this->builds(
                            $this->client->startkey(array($campagne, $societe_identifiant, $etablissement_identifiant))
                            ->endkey(array($campagne, $societe_identifiant, $etablissement_identifiant, array()))
                            ->getView($this->design, $this->view)->rows
                            );
    }

    public function builds($rows) {
        $drms = array();

        foreach($rows as $row) {
            $key = $row->key[self::KEY_ETABLISSEMENT_IDENTIFIANT] . '_' . $row->key[self::KEY_PERIODE] . '_' . $row->key[self::KEY_PRODUIT_HASH];
	    $drms[$key] = $this->build($row);
        }

        return $drms;
    }

    public function build($row) {
        $drm = new stdClass();
        $drm->societe_identifiant = $row->key[self::KEY_SOCIETE_IDENTIFIANT];
        $drm->etablissement_identifiant = $row->key[self::KEY_ETABLISSEMENT_IDENTIFIANT];
        $drm->declarant = new stdClass();
        $drm->declarant->nom = $row->value[self::VALUE_DECLARANT_NOM];
        $drm->campagne = $row->key[self::KEY_CAMPAGNE];
        $drm->periode = $row->key[self::KEY_PERIODE];
        $drm->version = $row->key[self::KEY_VERSION];
        $drm->produit_hash = $row->key[self::KEY_PRODUIT_HASH];
        $drm->produit_libelle = $row->value[self::VALUE_PRODUIT_LIBELLE];
        $drm->volume_stock_debut_mois = $row->value[self::VALUE_VOLUME_STOCK_DEBUT_MOIS];
        $drm->volume_entrees = $row->value[self::VALUE_VOLUME_ENTREES];
        $drm->volume_recolte = $row->value[self::VALUE_VOLUME_RECOLTE];
        $drm->volume_sorties = $row->value[self::VALUE_VOLUME_SORTIES];
        $drm->volume_facturable = $row->value[self::VALUE_VOLUME_FACTURABLE];
        $drm->volume_stock_fin_mois = $row->value[self::VALUE_VOLUME_STOCK_FIN_MOIS];

        return $drm;
    }
}  