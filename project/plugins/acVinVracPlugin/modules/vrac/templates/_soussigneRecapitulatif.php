<?php
use_helper('Vrac');
$cpt = 0;
?>
<div class="bloc_form bloc_form_condensed">
    <div id="soussigne_recapitulatif_vendeur" class="<?php echoClassLignesVisu($cpt);?> <?php echoPictoSignature($societe, $vrac, 'Vendeur', $template_validation); ?>">
        <label>Vendeur :</label>
        <span><a href="<?php echo url_for('vrac/recherche?identifiant=' . preg_replace('/ETABLISSEMENT-/', '', $vrac->vendeur_identifiant)) ?>"><?php echo $vrac->getVendeurObject()->getNom(); ?></a></span>
    </div>
    <div id="soussigne_recapitulatif_acheteur" class="<?php echoClassLignesVisu($cpt);?> <?php echoPictoSignature($societe, $vrac, 'Acheteur', $template_validation); ?>">
        <label>Acheteur :</label>
        <span><a href="<?php echo url_for('vrac/recherche?identifiant=' . preg_replace('/ETABLISSEMENT-/', '', $vrac->acheteur_identifiant)) ?>"><?php echo $vrac->getAcheteurObject()->getNom(); ?></a></span>
    </div>
<?php if (!$isTeledeclarationMode): ?>
        <div id="soussigne_recapitulatif_mandataire" class="<?php echoClassLignesVisu($cpt); ?>">
            <label>Contrat interne :</label>
            <span><?php echo ($vrac->interne) ? 'Oui' : 'Non'; ?></span>
        </div>
        <?php endif; ?>
    <div id="soussigne_recapitulatif_mandataire" class="<?php echoClassLignesVisu($cpt); ?> <?php echoPictoSignature($societe, $vrac, 'Courtier', $template_validation); ?>" >
        <?php if ($vrac->mandataire_identifiant != null && $vrac->mandataire_exist): ?>
            <label>Courtier&nbsp;:</label>
            <span><?php echo $vrac->getMandataireObject()->getNom(); ?></span>
<?php else: ?>
            Ce contrat ne possède pas de mandataire
<?php endif; ?>
    </div>
</div>
