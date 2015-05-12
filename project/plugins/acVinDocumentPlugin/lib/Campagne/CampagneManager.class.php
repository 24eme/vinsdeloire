<?php

class CampagneManager {

	protected $mm_dd_debut;

	public function __construct($mm_dd_debut) {
		$this->mm_dd_debut = $mm_dd_debut;
	}

	public function getCampagneByDate($date) {

        return $this->formatCampagneOutput(sprintf('%s-%s', date('Y', strtotime($this->getDateDebutByDate($date))), date('Y', strtotime($this->getDateFinByDate($date)))));
    }

    public function getCurrent() {

        return $this->getCampagneByDate(date('Y-m-d'));
    }

    public function getDateDebutByCampagne($campagne) {
        $campagne = $this->formatCampagneInput($campagne);

        $annees = $this->getAnnees($campagne);

        return $annees[1]."-".$this->mm_dd_debut; 
    }

    public function getDateFinByCampagne($campagne) {
        $campagne = $this->formatCampagneInput($campagne);

        $date_debut = new DateTime($this->getDateDebutByCampagne($campagne));

        return $date_debut->modify("+1 year")->modify("-1 day")->format('Y-m-d');
    }

    public function getDateDebutByDate($date) {
        $annee = date('Y', strtotime($date));

        while($date < $annee."-".$this->mm_dd_debut) {
        	$annee = $annee - 1;
        }

        return $annee."-".$this->mm_dd_debut;
    }

    public function getDateFinByDate($date) {
    	$date_debut = new DateTime($this->getDateDebutByDate($date));

    	return $date_debut->modify("+1 year")->modify("-1 day")->format('Y-m-d');
    }

    public function getPrevious($campagne) {
        $annees = $this->getAnnees($campagne);

        return $this->formatCampagneOutput(sprintf('%s-%s', $annees[1]-1, $annees[2]-1)); 

    }

    public function getNext($campagne) {
        $campagne = $this->formatCampagneInput($campagne);

        $annees = $this->getAnnees($campagne);

        return $this->formatCampagneOutput(sprintf('%s-%s', $annees[1]+1, $annees[2]+1)); 

    }

    protected function getAnnees($campagne) {
        $campagne = $this->formatCampagneInput($campagne);
        
    	if (!preg_match('/^([0-9]+)-([0-9]+)$/', $campagne, $annees)) {

            throw new sfException('campagne bad format');
        }

        return $annees;
    }

    protected function formatCampagneOutput($campagne_output) {

        return $campagne_output;
    }

    protected function formatCampagneInput($campagne_input) {

        return $campagne_input;
    }

    public function consoliderCampagnesList($campagnes, $add_current = true, $add_one_more = true) {
        krsort($campagnes);
        
        $campagnes_consolider = array();
        
        if($add_current) {
            $campagnes_consolider[$this->getCurrent()] = $this->getCurrent();
        }

        foreach($campagnes as $campagne => $value) {
            if(!$campagne) {

                    continue;
            }

            $next_campagne = $this->getNext($campagne);
            if($next_campagne < $this->getCurrent() && !array_key_exists($next_campagne, $campagnes_consolider)) {
                $campagnes_consolider[$next_campagne] = $next_campagne;
            }
            $campagnes_consolider[$campagne] = $campagne;
        }
        if(isset($campagne) && $add_one_more) {
            $campagnes_consolider[$this->getPrevious($campagne)] = $this->getPrevious($campagne);
        }

        krsort($campagnes_consolider);

        return $campagnes_consolider;
    }

}