<div id="contenu" style="background: #fff;">
	<section id="principal">
		<form  class="drm_labels_form" method="post" action="<?php echo url_for("drm_edition_produit_addlabel", $form->getObject()) ?>" >
		<div style="margin-bottom: 10px">
			<?php echo $form->renderHiddenFields(); ?>
			<?php echo $form->render(); ?>
		</div>
		<a href="<?php echo url_for('drm_edition_detail', $detail); ?>" id="drm_labels_annuler" class="btn_majeur btn_annuler">Abandonner</a>
		<button type="submit" class="btn_majeur btn_valider" >Valider</button>
		</form>
	</section>
</div>