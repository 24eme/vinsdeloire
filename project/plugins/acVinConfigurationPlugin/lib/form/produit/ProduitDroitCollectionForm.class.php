<?php
class ProduitDroitCollectionForm extends sfForm
{
  public function configure()
  {
    if (!$droits = $this->getOption('droits'))
      throw new InvalidArgumentException('You must provide a droit node.');
    $hasItem = false;
    $key = 0;
    foreach ($droits as $droit) {
    	$this->embedForm ($key, new ProduitDroitForm(null, array('droit' => $droit)));
    	$hasItem = true;
    	$key++;
    }
    if ($hasItem) {
    	$key++;
    }
    $this->embedForm ($key, new ProduitDroitForm());
  	$nbDroit = $this->getOption('nb');
    if ($nbDroit && $nbDroit > $key) {
    	$key++;
    	for ($i = $key; $i <= $nbDroit; $i++) {
    		$this->embedForm ($key, new ProduitDroitForm());
    		$key++;
    	}
    } 
  }
}