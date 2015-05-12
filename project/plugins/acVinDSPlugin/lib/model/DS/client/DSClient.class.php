<?php

class DSClient extends acCouchdbClient {

    const STATUT_VALIDE = 'VALIDE';
    const STATUT_A_SAISIR = 'A_SAISIR';

    public static function getInstance() {
        return acCouchdbManager::getClient("DS");
    }

    public function buildId($identifiant, $periode) {
        return sprintf('DS-%s-%s', $identifiant, $periode);
    }

    public function buildPeriode($date) {
        preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $matches);
        
        return sprintf('%d%02d', $matches[1], $matches[2]);
    }

    public function buildDate($periode) {

        return sprintf('%4d-%02d-%02d', $this->getAnnee($periode), $this->getMois($periode), date("t", $this->getMois($periode)));
    }

    public function buildCampagne($periode) {

        return ConfigurationClient::getInstance()->buildCampagne($this->buildDate($periode));
    }

    public function getAnnee($periode) {

        return preg_replace('/([0-9]{4})([0-9]{2})/', '$1', $periode);
    }

    public function getMois($periode) {

        return preg_replace('/([0-9]{4})([0-9]{2})/', '$2', $periode);
    }
    
    public function createDateStock($date_stock) {
	if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $date_stock)) {
		return $date_stock;
        }
        $v = date_create_from_format('d/m/Y',$date_stock);
	if (!$v) {
		throw new sfException("Unexepected date format for $date_stock");
	}
        return $v->format('Y-m-d');
    }

    public function findOrCreateDsByEtbId($etablissementId, $date_stock) {
        $periode = $this->buildPeriode($this->createDateStock($date_stock));
        $ds = $this->findByIdentifiantAndPeriode($etablissementId, $periode);
	if($ds){
	  $ds->updateProduits();
	  return $ds;
        }
        $ds = new DS();
        $ds->date_emission = date('Y-m-d');
        $ds->date_stock = $this->createDateStock($date_stock);
        $ds->identifiant = $etablissementId;
        $ds->storeDeclarant();
        $ds->updateProduits();
        return $ds;
    }

    public function createOrFind($etablissementId, $date_stock) {
      throw sfException('createOrFind deprecated use findOrCreateDsByEtbId instead');
    }

    public function getHistoryByOperateur($etablissement) {
        return DSHistoryView::getInstance()->findByEtablissementDateSorted($etablissement->identifiant);
    }

    public function findByIdentifiantAndPeriode($identifiant, $periode) {

        return $this->find($this->buildId($identifiant, $periode));
    }

    public function findLastByIdentifiant($identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
		$result = DSHistoryView::getInstance()->findByEtablissement($identifiant);
		$tabDs = array();
        foreach($result as $id => $ds) {
        	$tabDs[$ds->key[DSHistoryView::KEY_PERIODE]] = $ds->id;
        }
        krsort($tabDs);
		if (count($tabDs) > 0) {
			reset($tabDs);
			return $this->find(current($tabDs), $hydrate);
		}
        return null;
    }
    
    public function getMaster($id) {
        return $this->find($id);
    }
    
    public function getLibelleFromId($id) {
       if (!preg_match('/^DS-[0-9]+-([0-9]{4})([0-9]{2})/', $id, $matches)) {
            
            return $id;
        }
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Orthographe','Date'));
        $origineLibelle = 'DS de';
        $annee = $matches[1];
        $mois = $matches[2];
        $date = $annee.'-'.$mois.'-01';
        $df = format_date($date,'MMMM yyyy','fr_FR');
        return elision($origineLibelle,$df);
    }
}
