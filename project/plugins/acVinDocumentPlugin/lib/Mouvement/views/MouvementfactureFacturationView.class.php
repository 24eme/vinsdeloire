<?php

class MouvementfactureFacturationView extends acCouchdbView
{
    
    const KEYS_FACTURE = 0;
    const KEYS_FACTURABLE = 1;
    const KEYS_REGION = 2;
    const KEYS_ETB_ID = 3;
    const KEYS_ORIGIN = 4;
    const KEYS_MATIERE = 5;
    const KEYS_PRODUIT_ID = 6;
    const KEYS_PERIODE = 7;
    const KEYS_CONTRAT_ID = 8;
    const KEYS_VRAC_DEST = 9;
    const KEYS_MVT_TYPE = 10;
    const KEYS_DETAIL_ID = 11;
                    
    const VALUE_PRODUIT_LIBELLE = 0;
    const VALUE_TYPE_LIBELLE = 1;
    const VALUE_VOLUME = 2;
    const VALUE_CVO = 3;
    const VALUE_DATE = 4;
    const VALUE_DETAIL_LIBELLE = 5;
    const VALUE_VRAC_DEST = 6;
    const VALUE_NUMERO = 7;
    const VALUE_ID_ORIGINE = 8;

    public static function getInstance() {

        return acCouchdbManager::getView('mouvementfacture', 'facturation');
    }
    
    public function getMouvementsByEtablissement($etablissement,$facturee, $facturable) {        
        return $this->client
            ->startkey(array($facturee,$facturable,$etablissement->region_viticole,$etablissement->identifiant))
            ->endkey(array($facturee,$facturable,$etablissement->region_viticole,$etablissement->identifiant, array()))
            ->reduce(false)
            ->getView($this->design, $this->view)->rows;
    }
    
    public function getMouvementsBySociete($societe,$facturee, $facturable) {
	return $this->client
	  ->startkey(array($facturee,$facturable,$societe->getRegionViticole(),$societe->identifiant.'00'))
	  ->endkey(array($facturee,$facturable,$societe->getRegionViticole(),$societe->identifiant.'99', array()))
	  ->reduce(false)
	  ->getView($this->design, $this->view)->rows;
    }
    
    public function getMouvementsBySocieteWithReduce($societe,$facturee, $facturable,$level)
    {
       return $this->consolidationMouvements($this->client
	  ->startkey(array($facturee,$facturable,$societe->getRegionViticole(),$societe->identifiant.'00'))
	  ->endkey(array($facturee,$facturable,$societe->getRegionViticole(),$societe->identifiant.'99', array()))
	  ->reduce(true)->group_level($level)
	  ->getView($this->design, $this->view)->rows);
    }

    protected function consolidationMouvements($rows) {
        foreach($rows as $row) {
            $rows_mouvements = $this->client
            ->startkey($row->key)
            ->endkey(array_merge($row->key, array(array())))
            ->reduce(false)
            ->getView($this->design, $this->view)->rows;

            $row->value[self::VALUE_ID_ORIGINE] = array();
            foreach($rows_mouvements as $row_mouvement) {
                $row->value[self::VALUE_ID_ORIGINE] = array_merge($row->value[self::VALUE_ID_ORIGINE], array($row_mouvement->value[self::VALUE_ID_ORIGINE]));
            } 
        }

        return $rows;
    }

    public function getMouvementsNonFacturesBySociete($societe) {
      return $this->buildMouvements($this->getMouvementsBySociete($societe, 0, 1));
    }
    
    public function getMouvements($facturee, $facturable,$level) {
        return $this->consolidationMouvements($this->client
            ->startkey(array($facturee,$facturable))
            ->endkey(array($facturee,$facturable, array()))
            ->reduce(true)->group_level($level)
            ->getView($this->design, $this->view)->rows);
    }

    public function getMouvementsFacturablesByRegions($facturee, $facturable,$region,$level) {
        return $this->consolidationMouvements($this->client
            ->startkey(array($facturee,$facturable,$region))
            ->endkey(array($facturee,$facturable,$region, array()))
            ->reduce(true)->group_level($level)
            ->getView($this->design, $this->view)->rows);
    }

    protected function buildMouvements($rows) {
        $mouvements = array();
        foreach($rows as $row) {
            $mouvements[] = $this->buildMouvement($row);
        }

        return $mouvements;
    }

    protected function buildMouvement($row) {
        $mouvement = new stdClass();
        $mouvement->date = $row->value[self::VALUE_DATE];
        $mouvement->produit_libelle = $row->value[self::VALUE_PRODUIT_LIBELLE];
        $mouvement->type_libelle = $row->value[self::VALUE_TYPE_LIBELLE];
        $mouvement->volume = $row->value[self::VALUE_VOLUME];
        $mouvement->detail_libelle = $row->value[self::VALUE_DETAIL_LIBELLE];
        $mouvement->cvo = $row->value[self::VALUE_CVO];        
        $mouvement->numero = $row->value[self::VALUE_NUMERO]; 
        $mouvement->prix_ht = $mouvement->cvo * -1 * $mouvement->volume;
        return $mouvement;
    }
}  
