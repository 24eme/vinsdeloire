<?php
class DRMDroits extends BaseDRMDroits {
  const DROIT_CVO = 'CVO';
  const DROIT_DOUANE = 'Douane';

  static $droit_entrees = array('entrees/crd');
  public static function getDroitEntrees() {
    return self::$droit_entrees;
  }
  static $droit_sorties = array('sorties/crd','sorties/factures');
  public static function getDroitSorties($merge = array()) {
    return array_merge(self::$droit_sorties, $merge);
  }
  static $droit_sorties_inter_rhone = array('sorties/vrac');
  public static function getDroitSortiesInterRhone() {
    return self::$droit_sorties_inter_rhone;
  }

  private $res = array();
  private function addVirtual($key, $value) {
    if (!isset($this->res[$key]))
      $this->res[$key] = new DRMDroit($value->getDefinition(), $value->getDocument(), $value->getHash());
    $this->res[$key]->integreVirtualVolume($value);
    $this->res[$key]->code = $key;
    $this->res[$key]->libelle = (ConfigurationClient::getCurrent()->droits->exist($key))? ConfigurationClient::getCurrent()->droits->get($key) : $key;
  }

  public function getDroitsWithVirtual() {
    $this->res = $this->toArray();
    foreach($this->toArray() as $key => $value) {
      if (preg_match('/^([^_]+)_/', $key, $m)) {
	$this->addVirtual($m[1], $value);
      }
      $this->addVirtual('ZZZZTotal', $value);
    }
    if (count($this->res) == 2) {
      unset($this->res['ZZZZTotal']);
    }
    krsort($this->res);
    if (isset($this->res['ZZZZTotal'])) {
	$this->res['Total'] = $this->res['ZZZZTotal'];
	unset(	$this->res['ZZZZTotal'] );
	$this->res['Total']->code = 'Total';
	$this->res['Total']->libelle	 = 'Total';
    }
    return $this->res;
  }

  public function getCumul() {
    $sum = 0;
    foreach ($this->toArray() as $key => $value) {
      $sum += $value->getCumul();
    }
    return $sum;
  }
}