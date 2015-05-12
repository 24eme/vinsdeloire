<?php
/**
 * Description of class StockGenerationDeclarantForm
 * @author mathurin
 */
class DSGenerationOperateurForm extends BaseForm {

    private $anneeCampagneStart = 1991;
    
    public function configure()
    {
      $this->setWidget('date_declaration', new sfWidgetFormInput(array('default' => date('31/07/Y'))));
      $this->setValidator('date_declaration', new sfValidatorString());
        
        $this->widgetSchema->setLabels(array(
            'date_declaration' => 'Déclarations relatives aux stocks de vin au :'
        ));
        $this->widgetSchema->setNameFormat('ds_generation[%s]');
    }

    public function getCampagnes() {
        $annee = date('Y');
        
        $campagnes = array();
        for ($currentA = ($annee+1); $currentA > $this->anneeCampagneStart; $currentA--) {
            $elt = $currentA.'-'.($currentA+1);
            $campagnes[$elt] = $elt;
        }
        return $campagnes;
    }

}
