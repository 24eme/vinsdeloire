<form id="form_produit_declaration" method="post" action="<?php echo url_for("sv12_update_addProduit", $sv12) ?>">
   <p><?php echo $form->renderGlobalErrors(); echo $form->renderHiddenFields(); ?></p>
   <div>
   <div>Produit&nbsp;:<?php echo $form['hashref']->render(); echo $form['hashref']->renderError(); ?></div>
   <div><?php echo $form['withviti']->render(); ?><label for="sv12_add_produit_withviti">Affecter l'enlevement à un viti</label></div>
   <div class="lienviti">Raisins et moûts&nbsp;:<?php echo $form['raisinetmout']->render();  echo $form['raisinetmout']->renderError(); ?></div>
   <div class="lienviti">Viticulteur&nbsp;:<?php echo $form['identifiant']->render(); echo $form['identifiant']->renderError();?></div>
   </div>
<script>
$('#sv12_add_produit_withviti').change(function () {
if ($(this).is(':checked')) {
$('.lienviti').show();
}else{
$('.lienviti').hide();
}
});
</script>
	<div class="btn_etape">
		<a href="<?php echo url_for("sv12_update", $sv12) ?>" class="btn_etape_prec"><span>Annuler</span></a> 
		<button type="submit" id="ds_declaration_valid" class="btn_majeur btn_valider ds_declaration_addTemplate">Ajouter</button>
	</div>
</form>