<?php
class CampagneForm extends sfForm
{
	public function configure() 
	{    
        $this->setWidgets(array(
            'campagne'   => new sfWidgetFormChoice(array(
        						'choices' => $this->getCampagneChoices(),
        		))
        ));

        $this->setValidators(array(
            'campagne'   => new ValidatorCampagne()
        ));
        
        $this->widgetSchema->setNameFormat('campagne[%s]');
    }
    
    public function getCampagneChoices()
    {
    	$years = range(date('Y') + 1, date('Y') - 10);
    	$choices = array();
    	foreach ($years as $year) {
    		$choices[$year] = ($year - 1).'-'.$year;
    	}
    	return $choices;
    }
}