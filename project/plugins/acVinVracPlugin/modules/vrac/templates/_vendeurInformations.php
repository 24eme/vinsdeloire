<?php
use_helper('Display');
use_helper('Vrac');
?>
<script type="text/javascript">
    $(document).ready(function()
    {
        init_informations('vendeur');
<?php
if (!isset($numero_contrat)) {
    ?>
            ajaxifyGet('modification', '#vrac_vendeur_identifiant', '#vendeur_modification_btn', '#vendeur_informations');
    <?php
} else {
    ?>
            ajaxifyGet('modification', {field_0: '#vrac_vendeur_identifiant',
                'type': 'vendeur',
                'numero_contrat': "<?php echo $numero_contrat; ?>"
            }, '#vendeur_modification_btn', '#vendeur_informations');
    <?php
}
?>
        removeGreyPanel('acheteur');
        removeGreyPanel('mandataire');
        removeGreyPanel('has_mandataire');
        removeGreyPanel('ligne_btn');
        removeGreyPanel('interne');
    });
</script>
<?php if ($isTeledeclarationMode): ?>
    <div id="vendeur_infos" class="bloc_form bloc_form_condensed bloc_form_teledeclaration">    
        <div class="ligne_form ">
            <span>
                <label>Nom de l'acheteur :</label>
    <?php display_teledeclaration_soussigne_NomCvi($vendeur); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>Adresse :</label>
    <?php echo get_field($vendeur, 'siege/adresse') . '&nbsp' . get_field($vendeur, 'siege/code_postal') . '&nbsp' . get_field($vendeur, 'siege/commune'); ?>
            </span>
        </div>
    </div>
<?php else: ?>
    <div id="vendeur_infos" class="bloc_form bloc_form_condensed">
        <div class="ligne_form">
            <span>
                <label>Nom du vendeur :</label>
    <?php display_field($vendeur, 'nom'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>N° CVI</label>
    <?php display_field($vendeur, 'cvi'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>N° ACCISE</label>
    <?php display_field($vendeur, 'no_accises'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt " >
            <span>
                <label>TVA Intracomm.</label>
    <?php display_field($vendeur, 'no_tva_intracommunautaire'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>Adresse</label>
    <?php display_field($vendeur, 'siege/adresse'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>CP*</label>
    <?php display_field($vendeur, 'siege/code_postal'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>Ville*</label>
    <?php display_field($vendeur, 'siege/commune'); ?>
            </span>
        </div>
    </div>
<?php endif; ?>