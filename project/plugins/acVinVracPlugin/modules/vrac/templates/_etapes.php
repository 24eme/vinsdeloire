<?php
    if($vrac->etape==null) $vrac->etape=0;
    $pourcentage = ($vrac->etape) * 25;
?>
    <ol id="rail_etapes">
        <?php include_partial('etapeItem',array('num_etape' => 0,
                                                 'vrac' => $vrac,
                                                 'actif' => $actif,
                                                 'label' => 'Soussignés',
                                                 'url_etape' => 'vrac_soussigne',
                                                 'urlsoussigne' => $urlsoussigne
                                                )); ?>
        
        <?php include_partial('etapeItem',array('num_etape' => 1,
                                                 'vrac' => $vrac,
                                                 'actif' => $actif,
                                                 'label' => 'Marché',
                                                 'url_etape' => 'vrac_marche'
                                                )); ?>
        
        <?php include_partial('etapeItem',array('num_etape' => 2,
                                                 'vrac' => $vrac,
                                                 'actif' => $actif,
                                                 'label' => 'Conditions',
                                                 'url_etape' => 'vrac_condition'
                                                )); ?>
        
        <?php include_partial('etapeItem',array('num_etape' => 3,
                                                 'vrac' => $vrac,
                                                 'actif' => $actif,
                                                 'label' => 'Validation',
                                                 'url_etape' => 'vrac_validation'
                                                )); ?>
        
        
    </ol>