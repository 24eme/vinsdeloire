<div id="contrats_vrac" class="clearfix">
	<div class="ajout_annuaire">
		<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('annuaire_selectionner', array('identifiant' => $identifiant)); ?><?php if (isset($redirect)): ?>?redirect=<?php echo $redirect ?><?php endif; ?>">
			<h2 class="titre_principal">Ajouter un contact</h2>
	
			<div class="fond">
				<?php echo $form->renderHiddenFields() ?>
				<?php echo $form->renderGlobalErrors() ?>
				<p>Saisissez ici le type et l'identifiant du tiers que vous souhaitez ajouter à votre annuaire.</p><br />
				<table class="table_recap" id="">
						<thead>
							<tr>
								<th style="text-align: left; padding-left: 5px;">Type</th>
								<th style="text-align: left; padding-left: 5px;"><span>Identifiant</span></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align: left; padding-left: 5px;">
                                                            <?php if($isCourtierResponsable && (isset($form['type']))): ?>
									<span><?php echo $form['type']->renderError() ?></span>
									<?php echo $form['type']->render() ?>
                                                            <?php else: ?>
                                                                            Viticulteur
                                                                        <?php endif; ?>
								</td>
								<td style="text-align: left; padding-left: 5px;">
									<span><?php echo $form['tiers']->renderError() ?></span>
									<?php echo $form['tiers']->render() ?>
								</td>
							</tr>
						</tbody>
					</table>
			</div>
			<div style="margin: 10px 0; clear: both;">
	            <a style="float: left;" class="btn_orange btn_majeur" href="<?php echo url_for('annuaire_retour', array('identifiant' => $identifiant)) ?>">Retour</a>
		    	<button type="submit" name="valider" class="btn_vert btn_majeur" style="cursor: pointer; float: right;">
		    		Valider
		    	</button>
			</div>
		</form>
	</div>
</div>

<?php

include_partial('vrac/colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));

?>