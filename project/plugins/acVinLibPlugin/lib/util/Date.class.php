<?php

class Date {

	public static function diff($date1, $date2, $retour) {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
	    $diff = $date1->diff($date2);
	    return $diff->{$retour};
  	}
        
        public static function supEqual($date1, $date2) {
            $date1 = new DateTime($date1);
            $date2 = new DateTime($date2);
            return $date1 >= $date2;
        }

        public static function sup($date1, $date2) {
            $date1 = new DateTime($date1);
            $date2 = new DateTime($date2);
            return $date1 > $date2;
        }

          public static function getIsoDateFinDeMoisISO($date,$nb_mois) 
        {
            preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $matches);
            $annee = $matches[1];
            $mois = $matches[2];
            $lastdaymonth = mktime(0,0,0,$mois+$nb_mois+1,0,$annee);
            return date('Y-m-d', $lastdaymonth);
        }
        
        public static function addDelaiToDate($delai,$date=null) {
            if(!$date) $date = date('Y-m-d');
           return date('Y-m-d', strtotime($delai, strtotime($date)));
        }


        public static function getIsoDateFromFrenchDate($french_date) 
        {
            $matches = array();
	    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $french_date, $matches)) {
	      $jours = $matches[1];
	      $mois = $matches[2];
	      $annee = $matches[3];
	      return date('Y-m-d',mktime(0,0,0,$mois,$jours,$annee));
	    }
	    return $french_date;
        }
        
    	public static function francizeDate($date) 
    	{
    		if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $date, $matches)) {
	      		$jours = $matches[3];
	      		$mois = $matches[2];
	      		$annee = $matches[1];
	      		return date('d/m/Y',mktime(0,0,0,$mois,$jours,$annee));
    		}
    		return $date;
    	}
       
}
