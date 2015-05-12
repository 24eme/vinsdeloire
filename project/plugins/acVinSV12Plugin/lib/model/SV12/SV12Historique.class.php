<?php
class SV12Historique
{
	const VIEW_INDEX_ETABLISSEMENT = 0;
	const VIEW_CAMPAGNE = 1;
        const VIEW_PERIODE = 2;
	const VIEW_INDEX_VERSION = 3;
	const VIEW_INDEX_STATUS = 4;
	const VIEW_INDEX_STATUS_DOUANE_ENVOI = 5;
	const VIEW_INDEX_STATUS_DOUANE_ACCUSE = 6;
	const DERNIERE = 'DERNIERE';
	const CAMPAGNE = 'CAMPAGNE';
        const REGEXP_CAMPAGNE = '#^[0-9]{4}-[0-9]{2}$#';

	private $identifiant;
	private $campagne;
	private $drms;
	private $campagnes;
	
	public function __construct($identifiant, $campagne = null)
	{
		$this->identifiant = $identifiant;
		$this->campagne = $campagne;
	}
	
	public function getSliceDRMs($limit = 0) {

		return array_slice($this->getDRMs(), 0, $limit);
	}
	
	public function getDRMs($limite = 0)
	{
		if (!$this->drms) {
			$this->loadDRMs();
		}
		return $this->drms;
	}
	
	private function loadDRMs()
	{
		$drms = acCouchdbManager::getClient()
						->startkey(array($this->identifiant))
    					->endkey(array($this->identifiant, array()))
    					->reduce(false)
    					->getView("drm", "all")
    					->rows;

		$result = array();
		foreach ($drms as $drm) {
		  $result[$drm->id] = $drm->key;
		}
		krsort($result);

		$campagne = null;
		foreach($result as $key => $item) {
			$result[$key][self::CAMPAGNE] = $item[self::VIEW_CAMPAGNE];
			if ($result[$key][self::CAMPAGNE] != $campagne) {
				$result[$key][self::DERNIERE] = true;
				$campagne = $result[$key][self::CAMPAGNE];
			} else {
				$result[$key][self::DERNIERE] = false;
			}
		}
		$this->drms = $result;
	}
	
	public function getCampagnes()
	{
		if (!$this->campagnes) {
			$campagnes = array();
			$drms = $this->getDRMs();
	    	foreach ($drms as $drm) {
		  	if (!in_array($drm[self::CAMPAGNE], $campagnes)) {
	  				$campagnes[] = $drm[self::CAMPAGNE];
	    		}
	  		}
	  		rsort($campagnes);
	  		$this->campagnes = $campagnes;
		}
		return $this->campagnes;
	}
		
	public function getDRMsParCampagneCourante()
	{
		$drmsCampagne = array();
		$campagne = $this->getCampagneCourante();
		$drms = $this->getDRMs();
		foreach ($drms as $id => $drm) {
			if ($drm[self::CAMPAGNE] == $campagne) {
				$drmsCampagne[$id] = $drm;
			}
		}

		return $drmsCampagne;
	}
	
	public function getCampagneCourante()
	{
		if (!$this->campagne) {
			if($campagnes = $this->getCampagnes()) {
				$this->campagne = $campagnes[0];
			}
		}
		return $this->campagne;
	}
	
	public function getFutureDRM()
	{
		$lastDRM = current($this->getLastDRM());
		if (!$lastDRM) {
          $periode = DRMClient::getInstance()->buildPeriode(date('Y'), date('m'));
          $campagne = DRMClient::getInstance()->buildCampagne($periode);

		  return array(DRMClient::getInstance()->buildId($this->identifiant, $periode) => array($this->identifiant, $campagne, $periode, 0, null, null));
		}

		$periode = DRMClient::getInstance()->getPeriodeSuivante($lastDRM[self::VIEW_PERIODE]);
        $campagne = DRMClient::getInstance()->buildCampagne($periode);

        return array(
                        DRMClient::getInstance()->buildId($this->identifiant, $periode) =>  array($this->identifiant, $campagne, $periode, 0, null, null)
                    );
	}

	public function getLastDRM()
	{
		return $this->getSliceDRMs(1);
	}
	
	public function hasDRMInProcess()
	{
		$result = false;
		$drms = $this->getDRMs();
		foreach ($drms as $drm) {
			if (!$drm[self::VIEW_INDEX_STATUS]) {
				$result = true;
				break;
			}
		}
		return $result;
	}
	
	public function getIdentifiant() {
	  return $this->identifiant;
	}

	public function getNextByPeriode($periode)
	{
		$drms = $this->getDRMs();
		foreach ($drms as $drm) {
			if ($drm[self::VIEW_PERIODE] <= $periode) {

				return null;	
			} elseif (is_null($drm[self::VIEW_INDEX_STATUS])) {
				
				return $drm;
			}
		}
	}
	
	public function getPrevByPeriode($periode)
	{
		$drms = $this->getDRMs();

		$prevDrm = null;
		foreach ($drms as $drm) {
			if ($drm[self::VIEW_PERIODE] < $periode) {
				
				return $drm;
			}
		}
		return null;
	}
	
	public function getDateObjectByCampagne($campagne)
	{
		$this->checkCampagneFormat($campagne);
		$campagneTab = explode('-', $campagne);
		return new DateTime($campagneTab[0].'-'.$campagneTab[1].'-01');
	}
	
	public function checkCampagneFormat($campagne)
	{
		if (!preg_match(self::REGEXP_CAMPAGNE, $campagne)) {
			throw new sfException('La campagne doit être au format AAAA-MM');
		}
	}

}