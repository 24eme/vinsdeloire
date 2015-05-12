<?php 

class SV12Validation extends DocumentValidation {

    public function configure()
    {
      $this->addControle('vigilance', 'contrats_nonsoldes', 'Il reste des contrats à saisir');
      $this->addControle('vigilance', 'contrats_saisizero', 'Certains des volumes saisis sont à 0.00 hl');
    }

    public function controle() {
      $nbnonsoldes = count($this->document->getContratsNonSaisis());
      if ($nbnonsoldes) {
	     $this->addPoint('vigilance', 'contrats_nonsoldes', $nbnonsoldes.' contrat(s) soldé(s) trouvé(s)');
      }

      $nbsaisizero = 0;
      foreach($this->document->contrats as $key => $c) {
        if($c->volume === 0) {
          $nbsaisizero++;
        }
      }

      if($nbsaisizero) {
        $this->addPoint('vigilance', 'contrats_saisizero', $nbsaisizero.' contrat(s) trouvé(s)');
      }
    }
}