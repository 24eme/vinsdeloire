<?php
use_helper('Display');
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();

$type = $form->getObject()->getFamilleType();
$otherType = ($type=='acheteur')? 'vendeur' :  'acheteur';
?>
<script type="text/javascript">
    $(document).ready(function() {
        setGreyPanel('<?php echo $otherType;?>');
        setGreyPanel('has_mandataire');
        setGreyPanel('mandataire');
        setGreyPanel('ligne_btn');        
        setGreyPanel('interne');        
        init_ajax_modification('<?php echo $type;?>');
        bindEnterModif("<?php echo '#'.$type.'_infos'; ?>","<?php echo 'a#'.$type.'_modification_btn'; ?>"); 
    });                        
</script>



<div id="<?php echo $type; ?>_infos" class="modification_infos bloc_form">
    
    <div class="col">
        <div class="ligne_form">
            <span><label><?php echo ($type=="vendeur")? 'Nom du vendeur ' : "Nom de l'acheteur "; ?> :</label>
            <?php echo $form->getObject()->nom; ?>    </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span><label>N° CVI </label>
                <?php echo $form->getObject()->cvi; ?>    </span>
        </div>
        <div class="ligne_form">
            <span><?php echo $form['no_accises']->renderError(); ?>
            <?php echo $form['no_accises']->renderLabel() ?>
            <?php echo $form['no_accises']->render() ?> </span>
        </div>
        <div class="ligne_form ligne_form_alt " >
            <span><?php echo $form['no_tva_intracommunautaire']->renderError(); ?>
            <?php echo $form['no_tva_intracommunautaire']->renderLabel() ?>
            <?php echo $form['no_tva_intracommunautaire']->render() ?> </span>
        </div>
    </div>
    
    <div class="col">
        <div class="ligne_form">
            <span>
            <?php echo $form['siege']['adresse']->renderError(); ?>
            <?php echo $form['siege']['adresse']->renderLabel() ?>
            <?php echo $form['siege']['adresse']->render() ?> </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span><?php echo $form['siege']['code_postal']->renderError(); ?>
            <?php echo $form['siege']['code_postal']->renderLabel() ?>
            <?php echo $form['siege']['code_postal']->render() ?></span>  
        </div>
        <div class="ligne_form">
            <span>
            <?php echo $form['siege']['commune']->renderError(); ?>
            <?php echo $form['siege']['commune']->renderLabel() ?>
            <?php echo $form['siege']['commune']->render() ?> </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            
        </div>
    </div>
<div>
