<span><?php echo $form->getObject()->getLabelsLibelle(); ?> <?php echo $form->getObject()->label_supplementaire ?></span>

<?php if($form->getObject()->canSetLabels()): ?>
<a href="<?php echo url_for("drm_edition_produit_addlabel", $form->getObject()) ?>" class="btn_edition_label labels_lien" title="Choix du/des labels">Editer</a>
<?php endif; ?>
