<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class CompteAllView
 * @author mathurin
 */
class CompteAllView extends acCouchdbView {

    const KEY_INTERPRO_ID = 0;
    const KEY_STATUT = 1;
    const KEY_ID = 2;
    const KEY_NOM_A_AFFICHER = 3;
    const KEY_IDENTIFIANT = 4;
    const KEY_ADRESSE = 5;
    const KEY_COMMUNE = 6;
    const KEY_CODE_POSTAL = 7;
    const KEY_COMPTE_TYPE = 8;
    
    public static function getInstance() {
        return acCouchdbManager::getView('compte', 'all', 'Compte');
    }

    public function findByInterpro($interpro, $q = null, $limit = 100) {
      try {
	return $this->findByInterproELASTIC($interpro, $q, $limit);
      }catch(Exception $e) {
	return $this->findByInterproVIEW($interpro);
      }
    }
    
    public function findByInterproAndStatut($interpro, $q = null, $limit = 100, $statut = CompteClient::STATUT_ACTIF) {
      try {
	return $this->findByInterproELASTIC($interpro, $q, $limit, array(sprintf('statut:%s', $statut)));
      }catch(Exception $e) {
	return $this->findByInterproAndStatutVIEW($interpro, $statut);
      }
    }

    private function findByInterproELASTIC($interpro, $qs = null, $limit = 100, $query = array()) {
      $index = acElasticaManager::getType('Compte');
      $q = new acElasticaQuery();

      if($qs) {
	foreach(explode(' ', $qs) as $qs) {
	  $query[] = '*'.$qs.'*';
	}

        $qs = implode(' ', $query);
	$elasticaQueryString = new acElasticaQueryQueryString($qs);
	$q->setQuery($elasticaQueryString);
      }

      $q->setLimit($limit);

      //Search on the index.
      $res = $index->search($q);
      $viewres = $this->elasticRes2View($res);
      return $viewres;
    }

    private function elasticRes2View($results) {
      $res = array();
      foreach ($results->getResults() as $er) {
	$r = $er->getData();
	$e = new stdClass();
	$e->id = $r['_id'];
	$e->key = array($r['interpro'], $r['statut'], $r['_id'], $r['nom_a_afficher'], $r['identifiant'], $r['adresse'], $r['commune'], $r['code_postal'], $r['compte_type']);
	$e->value = null;
	$res[] = $e;
      }
      return $res;
    }


    public function findByInterproVIEW($interpro) {
        return $this->client->startkey(array($interpro))
                        ->endkey(array($interpro, array()))
                        ->getView($this->design, $this->view)->rows;
    }

//    public function findByInterproAndId($interpro,$id) {
//
//        return $this->client->startkey(array($interpro,$id))
//                        ->endkey(array($interpro,$id, array()))
//                        ->getView($this->design, $this->view);
//    }
//    
      public function findByInterproAndStatutVIEW($interpro,$statut) {
        return $this->client->startkey(array($interpro,$statut))
                        ->endkey(array($interpro,$statut, array()))
                        ->getView($this->design, $this->view)->rows;
    }
    
    
    public static function makeLibelle($datas) {
        $libelle = '';
        if (isset($datas[self::KEY_NOM_A_AFFICHER]) && $nom = $datas[self::KEY_NOM_A_AFFICHER]) {
            if ($libelle) {
                $libelle .= ' / ';
            }
            $libelle .= $nom;
        }

        $libelle .= ' (' . $datas[self::KEY_ADRESSE];
        if (isset($datas[self::KEY_ADRESSE]) && $adresse = $datas[self::KEY_ADRESSE]) {
            $libelle .= ' / ' . $adresse;
        }

        if (isset($datas[self::KEY_COMMUNE]) && $commune = $datas[self::KEY_COMMUNE]) {
            $libelle .= ' / ' . $commune;
        }
        
        if (isset($datas[self::KEY_CODE_POSTAL]) && $cp = $datas[self::KEY_CODE_POSTAL]) {
            $libelle .= ' / ' . $cp;
        }
        $libelle .= ') ';

	$libelle .= $datas[self::KEY_COMPTE_TYPE].' - '.$datas[self::KEY_IDENTIFIANT];

        return trim($libelle);
    }

}

