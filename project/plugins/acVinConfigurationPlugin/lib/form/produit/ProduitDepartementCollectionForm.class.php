<?php
class ProduitDepartementCollectionForm extends sfForm
{
  public function configure()
  {
    if (!$departements = $this->getOption('departements'))
      throw new InvalidArgumentException('You must provide a departement node.');
    $hasItem = false;
    $key = 0;
    foreach ($departements as $departement) {
    	$this->embedForm ($key, new ProduitDepartementForm(null, array('departement' => $departement)));
    	$hasItem = true;
    	$key++;
    }
    if ($hasItem) {
    	$key++;
    }
    $this->embedForm ($key, new ProduitDepartementForm());
    $nbDepartement = $this->getOption('nb');
    if ($nbDepartement && $nbDepartement > $key) {
    	$key++;
    	for ($i = $key; $i <= $nbDepartement; $i++) {
    		$this->embedForm ($key, new ProduitDepartementForm());
    		$key++;
    	}
    }
  }
}