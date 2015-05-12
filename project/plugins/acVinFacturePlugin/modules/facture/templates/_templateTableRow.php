<?php
use_helper('Float');
use_helper('Display');
$produit->origine_libelle = str_replace("&#039;", "'", $produit->origine_libelle);    
?>
~~~~\truncate{124mm}{\small{\textbf{<?php echo $produit->produit_libelle.'} '.escape_string_for_latex($produit->origine_libelle); ?> }} &
                            \multicolumn{1}{r|}{\small{<?php echoArialFloat($produit->volume*-1); ?>}} &
                            \multicolumn{1}{r|}{\small{<?php echoArialFloat($produit->cotisation_taux); ?>}} & 
                            \multicolumn{1}{r|}{\small{<?php echoArialFloat($produit->montant_ht); ?>}}&
                            \multicolumn{2}{c}{\small{<?php echo $produit->echeance_code; ?>}} \\