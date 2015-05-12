<section id="principal">
	<h2>Annuaire de vos contacts</h2>

	<div class="fond">
		<div class="annuaire clearfix">
		
			<div class="bloc_annuaire">
				
				<table class="table_recap" id="">			
					<thead>
						<tr>
							<th colspan="2" style="text-align: left; padding-left: 5px;">Viticulteurs (<?php echo count($annuaire->recoltants) ?>)</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($annuaire->recoltants) > 0): ?>
						<?php foreach ($annuaire->recoltants as $key => $item): ?>
						<tr>
							<td style="text-align: left; padding-left: 5px;"><?php echo $item ?> <span style="color: #808080; font-size: 11px;">(<?php echo $key; ?>)</span></td>
							<td><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key, 'identifiant' => $etablissement->identifiant)) ?>" onclick="return confirm('Confirmez-vous la suppression du viticulteur ?')" class="btn_supprimer">X</a></td>
						</tr>
						<?php endforeach; ?>
						<?php else: ?>
							<tr><td style="text-align: left; padding-left: 5px;"><span style="font-style: italic; font-size: 11px;">Aucun viticulteur</span></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
				<div style="text-align: right; margin: 10px 0;">
					<a href="<?php echo url_for('annuaire_selectionner', array('type' => 'recoltants', 'identifiant' => $etablissement->identifiant)) ?>" class="btn_vert btn_majeur">Ajouter un viticulteur</a>
				</div>
			</div>
		<?php if($isCourtierResponsable): ?>
			<div class="bloc_annuaire">
			
				<table class="table_recap" id="">			
					<thead>
						<tr>
							<th colspan="2" style="text-align: left; padding-left: 5px;">Négociants (<?php echo count($annuaire->negociants) ?>)</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($annuaire->negociants) > 0): ?>
						<?php foreach ($annuaire->negociants as $key => $item): ?>
						<tr>
							<td style="text-align: left; padding-left: 5px;"><?php echo $item ?> <span style="color: #808080; font-size: 11px;">(<?php echo $key; ?>)</span></td>
							<td><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key, 'identifiant' => $etablissement->identifiant)) ?>" onclick="return confirm('Confirmez-vous la suppression du négociant ?')" class="btn_supprimer">X</a></td>
						</tr>
						<?php endforeach; ?>
						<?php else: ?>
							<tr><td style="text-align: left; padding-left: 5px;"><span style="font-style: italic; font-size: 11px;">Aucun négociant</span></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
				<div style="text-align: right; margin: 10px 0;">
					<a href="<?php echo url_for('annuaire_selectionner', array('type' => 'negociants', 'identifiant' => $etablissement->identifiant)) ?>" class="btn_vert btn_majeur">Ajouter un négociant</a>
				</div>
			</div>                
			<div class="bloc_annuaire">
			
				<table class="table_recap" id="">			
					<thead>
						<tr>
							<th colspan="2" style="text-align: left; padding-left: 5px;">Commerciaux (<?php echo count($annuaire->commerciaux) ?>)</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($annuaire->commerciaux) > 0): ?>
						<?php foreach ($annuaire->commerciaux as $key => $item): ?>
						<tr>
							<td style="text-align: left; padding-left: 5px;"><?php echo $item ?> <span style="color: #808080; font-size: 11px;">(<?php echo $key; ?>)</span></td>
							<td><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'commerciaux', 'id' => $key, 'identifiant' => $etablissement->identifiant)) ?>" onclick="return confirm('Confirmez-vous la suppression du commercial ?')" class="btn_supprimer">X</a></td>
						</tr>
						<?php endforeach; ?>
						<?php else: ?>
							<tr><td style="text-align: left; padding-left: 5px;"><span style="font-style: italic; font-size: 11px;">Aucun commercial</span></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
				<div style="text-align: right; margin: 10px 0;">
					<a href="<?php echo url_for('annuaire_commercial_ajouter', array('identifiant' => $etablissement->identifiant)) ?>" class="btn_vert btn_majeur">Ajouter un commercial</a>
				</div>
			</div>
                    <?php endif; ?>
		</div>
	</div>

	<a class="btn_orange btn_majeur" href="<?php echo url_for('vrac_societe', array('identifiant' => $etablissement->identifiant)) ?>">Retourner à l'espace contrats</a>

</section>

<?php

include_partial('vrac/colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));

?>

