<form id="form_produit_declaration" method="post" action="<?php echo url_for("ds_edition_operateur_addProduit", $ds) ?>">
	<?php echo $form ?>
	<div class="btn_etape">
		<a href="<?php echo url_for("ds_edition_operateur", $ds) ?>" class="btn_etape_prec"><span>Annuler</span></a> 
		<button type="submit" id="ds_declaration_valid" class="btn_majeur btn_valider ds_declaration_addTemplate">Ajouter</button>
	</div>
</form>