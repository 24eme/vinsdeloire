<form id="form_produit_declaration" method="post" action="<?php echo url_for("drm_edition_produit_ajout", $drm) ?>" class="section_label_strong">
    <label>Saisir une appellation</label>
    
    <?php echo $form['hashref']->render(); ?>
    <?php echo $form->renderHiddenFields(); ?>
</form>