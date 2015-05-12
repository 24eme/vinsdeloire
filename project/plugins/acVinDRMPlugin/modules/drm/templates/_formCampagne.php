<form  class="popup_form" id="form_ajout" action="<?php echo url_for('@drm_etablissement') ?>" method="post">
	<?php echo $form->renderHiddenFields() ?>
	<div class="ligne_form">
		<span class="error"><?php echo $form->renderGlobalErrors() ?></span>
		<?php echo $form['months']->render() ?> / <?php echo $form['years']->render() ?>
	</div>
	<div class="ligne_form_btn">
		<button name="valider" class="btn_valider" type="submit">Valider</button>
	</div>
</form>