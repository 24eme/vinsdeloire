<form id="form_appellation_ajout" method="post" action="<?php echo url_for('drm_recap_lieu_ajout_ajax', $certification) ?>" class="popup_form" data-popup-success="popup" data-popup="#popup_ajout_detail" data-popup-config="configForm" data-popup-titre="Ajouter un produit">
	<?php echo $form->renderGlobalErrors() ?>
	<?php echo $form->renderHiddenFields() ?>
	<div class="ligne_form">
		<span class="error"><?php echo $form['hash']->renderError() ?></span>
		<?php echo $form['hash']->renderLabel() ?> 
		<?php echo $form['hash']->render() ?>
	</div>
	<div class="ligne_form_btn">
		<a href="#" onClick="location.reload(true); return false;" name="annuler" class="btn_annuler">Annuler</a>
		<button name="valider" class="btn_valider" type="submit">Valider</button>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function () {
	$( "#<?php echo $form['hash']->renderId() ?>" ).combobox();
});
</script>