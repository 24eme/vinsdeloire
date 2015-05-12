<tr>
    <td>
    	<?php if (!$form->getObject()->total_debut_mois && !$form->getObject()->hasStockFinDeMoisDRMPrecedente()): ?>
    	<a href="<?php echo url_for('drm_mouvements_generaux_produit_delete', $form->getObject()) ?>" class="supprimer">Supprimer</a>
    	<?php endif; ?>
    	<?php echo $form->getObject()->getLibelle(); ?>
    </td>
	<td>
        <?php echo $form->getObject()->total_debut_mois ?> <span class="unite">hl </span>
	</td>
	<td>
		<form class="updateProduct" action="<?php echo url_for('drm_mouvements_generaux_produit_update', $form->getObject()) ?>" method="post">
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form['pas_de_mouvement_check']->render(array("class" => "pas_de_mouvement")) ?>
		</form>
	</td>
</tr>