<?php
/**
 * Model for DRMDroit
 *
 */


class DRMDroit extends BaseDRMDroit {
  private $virtual = 0;
  private $payable_total = 0;
  private $cumulable_total = 0;

  public function setTaux($taux) {
    if ($taux <= 0) {
      return;
    }
    return $this->_set('taux', $taux);
  }

  public function integreVirtualVolume($drmdroit) {
    $this->virtual = 1;
    $this->integreVolume($drmdroit->volume_taxe, $drmdroit->volume_reintegre, '', $drmdroit->report, '');
    $this->payable_total += $drmdroit->getPayable();
    $this->cumulable_total += $drmdroit->getCumulable();
  }

  public function integreVolume($volume_taxable, $volume_reintegre, $taux, $report, $libelle) {
  	if (!$this->libelle && $libelle) {
      $this->libelle = $libelle;
    }
    if (!$this->taux && $taux) {
      $this->taux = $taux;
    }
    if (!$this->code) {
      $this->code = $this->key;
    }
    $this->volume_taxe += $volume_taxable;
    $this->volume_reintegre += $volume_reintegre;
    $this->report += $report;
  	$this->total = ($this->volume_taxe - $this->volume_reintegre) * $this->taux;
  	$this->cumul = $this->total + $this->report;
  }
  
  public function getPayable() {
    if ($this->virtual)
      return $this->payable_total;
    return $this->total;
  }
  public function getCumulable() {
    if ($this->virtual) {
      return $this->cumulable_total;
    }
    return $this->cumul;
  }
  public function isTotal() {
    return ($this->virtual) || !preg_match('/_/', $this->code);
  }
  public function isVirtual() {
    return ($this->virtual);
  }
  public function isReportable() {
  	return ($this->getPaiement()->isAnnuelle() && $this->isDroit('douane'))? true : false;
  }
  public function isDroit($type) {
  	return ($this->getParent()->getKey() == $type)? true : false;
  }
  public function getPaiement() {
  	return $this->getDocument()->get('declaratif')->get('paiement')->get($this->getParent()->getKey());
  }
}