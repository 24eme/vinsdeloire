<?php
/**
 * Model for DRMPaiement
 *
 */

class DRMPaiement extends BaseDRMPaiement 
{
  const FREQUENCE_ANNUELLE = 'Annuelle';
  const FREQUENCE_MENSUELLE = 'Mensuelle';
  const NUM_MOIS_DEBUT_CAMPAGNE = 8;
  
  public function isAnnuelle() 
  {
  	return ($this->frequence == self::FREQUENCE_ANNUELLE)? true : false;
  }
  
  public static function isDebutCampagne($numeroMois = null) 
  {
  	if (!$numeroMois) {
  		$numeroMois = date('m');
  	}
  	return ($numeroMois == self::NUM_MOIS_DEBUT_CAMPAGNE)? true : false;
  }

}