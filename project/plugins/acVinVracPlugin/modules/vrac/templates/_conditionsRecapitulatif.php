<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use_helper('Vrac');
use_helper('Float');
use_helper('Date');
$cpt = 0;
?>
<div class="bloc_form bloc_form_condensed">
    <div id="conditions_recapitulatif_typeContrat" class="<?php echoClassLignesVisu($cpt); ?>">
        <label>Type de contrat&nbsp;:</label>
        <span><?php echo $vrac->type_contrat; ?></span>
    </div>
    <?php if (!$isTeledeclarationMode): ?>
        <div id="conditions_recapitulatif_isvariable" class="<?php echoClassLignesVisu($cpt); ?>">
            <label>Prix variable :</label>
            <span><?php
                echo ($vrac->prix_variable) ? 'Oui' : 'Non';
                echo ($vrac->prix_variable) ? ' (' . $vrac->part_variable . '%)' : '';
                ?>
            </span>
        </div>

        <div id="conditions_cvo_nature" class="<?php echoClassLignesVisu($cpt); ?>">
            <label>Nature de la transaction&nbsp;: </label>
            <span><?php echo $vrac->cvo_nature ?></span>
        </div>
    <?php endif; ?>
    
    <?php
    /**
     * Après CVO => admin doivent pouvoir voir ça
     */
    $isAdmin = false;
    if (!$isTeledeclarationMode || $isAdmin):
        ?>
        <div id="conditions_recapitulatif_cvo" class="<?php echoClassLignesVisu($cpt); ?>">
            <label>Repartition de la CVO&nbsp;: </label>
            <span><?php echo VracClient::$cvo_repartition[$vrac->cvo_repartition] ?></span>
        </div>
<?php endif; ?> 

    <?php if (!$isTeledeclarationMode): ?>
        <?php if ($vrac->date_signature) : ?>
            <div id="conditions_recapitulatif_date_signature" class="<?php echoClassLignesVisu($cpt); ?>">
                <label>Date de signature&nbsp;: </label>
                <span><?php echo $vrac->date_signature; ?></span>
            </div>
    <?php endif; ?> 

        <?php if ($vrac->date_campagne && !$sf_user->hasTeledeclarationVrac()) : ?>
            <div id="conditions_recapitulatif_date_saisie" class="<?php echoClassLignesVisu($cpt); ?>">
                <label>Date de campagne (statistique)&nbsp;: </label>
                <span><?php echo $vrac->date_campagne; ?></span>
            </div>
        <?php endif; ?> 

        <?php if ($vrac->valide->date_saisie) : ?>
            <div id="conditions_recapitulatif_date_saisie" class="<?php echoClassLignesVisu($cpt); ?>">
                <label>Date de saisie&nbsp;: </label>
                <span><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?></span>
            </div>
        <?php endif; ?> 
    <?php else: ?> 
          <div id="conditions_recapitulatif_enlevement_date" class="<?php echoClassLignesVisu($cpt); ?>">
                <label>Date d'enlèvement&nbsp;: </label>
                <span><?php echo format_date($vrac->getMaxEnlevement(), 'dd/MM/yyyy'); ?></span>
            </div>
              <div id="conditions_recapitulatif_enlevement_frais_garde" class="<?php echoClassLignesVisu($cpt); ?>">
                <label>Frais de garde par mois&nbsp;: </label>
                <span><?php echo ($vrac->exist('enlevement_frais_garde'))? echoF($vrac->enlevement_frais_garde)."&nbsp;€/hl" : ''."&nbsp;€/hl"; ?></span>
            </div>
    <?php endif; ?> 
    <?php if (!$isTeledeclarationMode): ?>
        <div id="conditions_recapitulatif_commentaire" class="<?php echoClassLignesVisu($cpt); ?>">
            <label>Commentaires&nbsp;: </label>
            <pre class="commentaire"><?php echo $vrac->commentaire; ?></pre>
        </div>
<?php endif; ?> 
</div>
