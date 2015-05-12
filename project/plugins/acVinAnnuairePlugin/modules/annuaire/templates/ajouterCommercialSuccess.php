<div id="contrats_vrac">

	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('annuaire_commercial_ajouter', array('identifiant' => $identifiant)) ?>">
		<h2 class="titre_principal">Ajouter un commercial</h2>
		<div class="fond">
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->renderGlobalErrors() ?>
			<p>Saisissez ici l'identité et les coordonnées de l'interlocuteur commercial que vous souhaitez ajouter.</p><br />
			<table class="table_recap" id="">
				<thead>
					<tr>
						<th style="text-align: left; padding-left: 5px;">Identité</th>
						<th style="text-align: left; padding-left: 5px;"><span>Email</span></th>
						<th style="text-align: left; padding-left: 5px;"><span>Téléphone</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align: left; padding-left: 5px;">
							<span><?php echo $form['identite']->renderError() ?></span>
							<?php echo $form['identite']->render() ?>
						</td>
						<td style="text-align: left; padding-left: 5px;">
							<span><?php echo $form['email']->renderError() ?></span>
							<?php echo $form['email']->render() ?>
						</td>
						<td style="text-align: left; padding-left: 5px;">
							<span><?php echo $form['telephone']->renderError() ?></span>
							<?php echo $form['telephone']->render() ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div style="margin: 10px 0; clear: both;">
            <a style="float: left;" class="btn_orange btn_majeur" href="<?php echo url_for('annuaire', array('identifiant' => $identifiant)) ?>">Retour</a>
	    	<button type="submit" name="valider" class="btn_vert btn_majeur" style="cursor: pointer; float: right;">
	    		Valider
	    	</button>
		</div>
	</form>
</div>

<?php

include_partial('vrac/colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));

?>