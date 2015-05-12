<?php
use_helper('Display');
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();

$type = $form->getObject()->getFamilleType();
?>
<script type="text/javascript">
    $(document).ready(function() {
        init_ajax_modification('<?php echo $type;?>');
        
        setGreyPanel('vendeur');
        setGreyPanel('acheteur');
        setGreyPanel('has_mandataire');
        setGreyPanel('ligne_btn');
        setGreyPanel('interne');  
        bindEnterModif('.mandataire_infos','a#mandataire_modification_btn'); 
    });                        
</script>


<div class="mandataire_infos bloc_form bloc_form_condensed">
    <div class="ligne_form">
        <span>
            <label>Nom du <?php echo $type; ?> :</label>
            <?php echo $form->getObject()->nom; ?> 
        </span>
    </div>
    <div class="ligne_form ligne_form_alt">    
        <span>
            <?php echo $form['carte_pro']->renderError(); ?>
            <?php echo $form['carte_pro']->renderLabel() ?>
            <?php echo $form['carte_pro']->render() ?> 
        </span>
    </div>
    <div class="ligne_form">       
        <span>
            <?php echo $form['siege']['adresse']->renderError(); ?>
            <?php echo $form['siege']['adresse']->renderLabel() ?>             
            <?php echo $form['siege']['adresse']->render() ?>       
        </span>
    </div>
    <div class="ligne_form ligne_form_alt"> 
        <span>
            <?php echo $form['siege']['code_postal']->renderError(); ?>
            <?php echo $form['siege']['code_postal']->renderLabel() ?>
            <?php echo $form['siege']['code_postal']->render() ?>  
        </span>
        
    </div>
    <div class="ligne_form">      
        <span>
            <?php echo $form['siege']['commune']->renderError(); ?>
            <?php echo $form['siege']['commune']->renderLabel() ?>
            <?php echo $form['siege']['commune']->render() ?>
        </span>
    </div>
</div>

