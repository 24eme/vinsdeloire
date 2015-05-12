<?php
use_helper('Display');
?>
<div id="operateur_infos" class="bloc_form">    
    <div class="col">
        <div class="ligne_form">
            <span>
                  <label>Nom de l'opérateur :</label>
                  <?php display_field($operateur,'nom'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>N° CVI</label>
                <?php display_field($operateur,'cvi'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>N° ACCISE</label>
                <?php display_field($operateur,'no_accises'); ?>
            </span>
        </div>
    </div>
    
    <div class="col">
        <div class="ligne_form">
            <span>
                <label>Adresse</label>
                <?php display_field($operateur,'adresse');  ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>CP*</label>
                <?php display_field($operateur,'code_postal'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>Ville*</label>
                <?php display_field($operateur,'commune'); ?>
            </span>
        </div>
    </div>
</div>