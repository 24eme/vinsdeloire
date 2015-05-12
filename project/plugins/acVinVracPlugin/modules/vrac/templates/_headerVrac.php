  <?php include_partial('fil_ariane', array('vrac' => $vrac, 'compte' => $compte, 'fil' => -1)); ?>
    <?php 
    $params = array('vrac' => $vrac, 'actif' => $actif);
    if(isset($urlsoussigne)): 
       $params['urlsoussigne'] =  $urlsoussigne;
    endif;
?>
<?php include_partial('etapes', $params); ?>