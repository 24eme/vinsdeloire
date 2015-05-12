<?php 

class DSValidation extends DocumentValidation
{
  public function configure() {
    $this->addControle('vigilance', 'stock_null', 'Il reste des stocks non saisis');
    $this->addControle('vigilance', 'stock_zero', 'Certains stocks saisis sont à 0.00 hl');
  }

  public function controle()
  {

    $nbnull = 0;
    $nbzero = 0;
    foreach($this->document->declarations as $key => $obj) {
      if (is_null($obj->stock_declare)) {
	      $nbnull++;
      }

      if ($obj->stock_declare === 0) {
        $nbzero++;
      }
    }

    if ($nbnull > 0) {
      $this->addPoint('vigilance', 'stock_null', $nbnull.' produit(s) concerné(s)', $this->generateUrl('ds_edition_operateur', $this->document)); 
    }

    if($nbzero > 0){
      $this->addPoint('vigilance', 'stock_zero', $nbzero.' produit(s) concerné(s)', $this->generateUrl('ds_edition_operateur', $this->document)); 
    }
  }

}