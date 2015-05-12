<?php
use_helper('Display');
use_helper('Vrac');
?>
<script type="text/javascript">
    $(document).ready(function()
    {
        init_informations('acheteur');
<?php
if (!isset($numero_contrat)) {
    ?>
            ajaxifyGet('modification', '#vrac_acheteur_identifiant', '#acheteur_modification_btn', '#acheteur_informations');
    <?php
} else {
    ?>
            ajaxifyGet('getInfos', {field_0: '#vrac_acheteur_identifiant',
                'type': 'acheteur',
                'numero_contrat': '<?php echo $numero_contrat; ?>'
            }, '#acheteur_modification_btn', '#acheteur_informations');
    <?php
}
?>
        removeGreyPanel('vendeur');
        removeGreyPanel('has_mandataire');
        removeGreyPanel('mandataire');
        removeGreyPanel('ligne_btn');
        removeGreyPanel('interne');
    });
</script>


<?php if ($isTeledeclarationMode): ?>
    <div id="acheteur_infos" class="bloc_form bloc_form_condensed bloc_form_teledeclaration">
        <div class="ligne_form ">
            <span>
                <label>Nom de l'acheteur :</label>
    <?php display_teledeclaration_soussigne_NomCvi($acheteur); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>Adresse :</label>
    <?php echo get_field($acheteur, 'siege/adresse') . '&nbsp' . get_field($acheteur, 'siege/code_postal') . '&nbsp' . get_field($acheteur, 'siege/commune'); ?>
            </span>
        </div>
    </div>
<?php else: ?>
    <div id="acheteur_infos" class="bloc_form bloc_form_condensed">  
        <div class="ligne_form ">
            <span>
                <label>Nom de l'acheteur :</label>
    <?php display_field($acheteur, 'nom'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>N° CVI</label>
    <?php display_field($acheteur, 'cvi'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>N° ACCISE</label>
    <?php display_field($acheteur, 'no_accises'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt " >
            <span>
                <label>TVA Intracomm.</label>
    <?php display_field($acheteur, 'no_tva_intracommunautaire'); ?>
            </span>
        </div>

        <div class="ligne_form">
            <span>
                <label>Adresse</label>
    <?php display_field($acheteur, 'siege/adresse'); ?>
            </span>
        </div>
        <div class="ligne_form ligne_form_alt">
            <span>
                <label>CP*</label>
    <?php display_field($acheteur, 'siege/code_postal'); ?>
            </span>
        </div>
        <div class="ligne_form">
            <span>
                <label>Ville*</label>
    <?php display_field($acheteur, 'siege/commune'); ?>
            </span>
        </div>
    </div>
<?php endif; ?>
