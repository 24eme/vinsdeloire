<?php
class ProduitLabelCollectionForm extends sfForm
{
  public function configure()
  {
    if (!$labels = $this->getOption('labels'))
      throw new InvalidArgumentException('You must provide a label node.');
    $hasItem = false;
    $key = 0;
    foreach ($labels as $code_label) {
    	$this->embedForm ($key, new ProduitLabelForm(null, array('code_label' => $code_label)));
    	$hasItem = true;
    	$key++;
    }
    if ($hasItem) {
    	$key++;
    }
    $this->embedForm ($key, new ProduitLabelForm());
    $nbLabel = $this->getOption('nb');
    if ($nbLabel && $nbLabel > $key) {
    	$key++;
    	for ($i = $key; $i <= $nbLabel; $i++) {
    		$this->embedForm ($key, new ProduitLabelForm());
    		$key++;
    	}
    }
  }
}