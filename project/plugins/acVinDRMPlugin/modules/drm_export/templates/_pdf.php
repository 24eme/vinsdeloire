<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="no-js">
<head>
	<title>DRM | Vins de Provence</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="author" content="Actualys" />
	<meta name="Description" content="" /> 
	<meta name="Keywords" content="" />
	<meta name="robots" content="index,follow" />
	<meta name="Content-Language" content="fr-FR" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="copyright" content="Vins de Provence - 2011" />

	<?php include_partial('drm_export/pdfCss') ?>
</head>

<body>
	<script type="text/php">
		if (isset($pdf)) {
			$w = $pdf->get_width();
			$h = $pdf->get_height();
			$font = Font_Metrics::get_font("helvetica");
			$pdf->page_text($w / 2 - 4, $h - 13, "f {PAGE_NUM} / {PAGE_COUNT}", $font, 8, array(0,0,0));
		}
	</script>
	<?php include_partial('drm_export/pdfHeader', array('drm' => $drm)); ?>
	<?php include_partial('drm_export/pdfFooter'); ?>

	<?php $i=0; ?>
	<?php foreach($drm->declaration->certifications as $certification_key => $certification): ?>
		<?php while($pagers_volume[$certification_key]->getPage() <= $pagers_volume[$certification_key]->getLastPage() || 
					$pagers_vrac[$certification_key]->getPage() <= $pagers_vrac[$certification_key]->getLastPage()): ?>

		<?php $colonnes = $pagers_volume[$certification_key]->getResults(); ?>
		<?php if(count($colonnes) > 0): ?>
			<h2>Suivi des vins - <?php echo $certification->getConfig()->libelle ?></h2>
			<table class="recap volumes bloc_bottom">
				<?php include_partial('drm_export/pdfLine', array('libelle' => 'Code produit',
	    						  								  'counter' => 1,
																  'colonnes' => $colonnes,
																  'cssclass_value' => 'libelle',
																  'partial' => 'drm_export/pdfLineVolumeItemProduitLibelle')) ?>

				<?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Total début de mois',
																	   'unite' => 'hl',
	    						  								       'counter' => 2,
																	   'cssclass_libelle' => 'total',
																       'cssclass_value' => 'total',
																       'colonnes' => $colonnes,
																       'hash' => 'total_debut_mois')) ?>

				<?php include_partial('drm_export/pdfLineDetail', array('certification_config' => $certification->getConfig(),
	    						  								        'counter' => 2,
																		'colonnes' => $colonnes,
																  		'hash' => 'stocks_debut')) ?>

				<?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Total entrées',
																	   'unite' => 'hl',
	    						  								       'counter' => 3,
																	   'cssclass_libelle' => 'total',
																  	   'cssclass_value' => 'total',
																	   'colonnes' => $colonnes,
																	   'hash' => 'total_entrees')) ?>


				<?php include_partial('drm_export/pdfLineDetail', array('certification_config' => $certification->getConfig(),
	    						  								        'counter' => 3,
																		'colonnes' => $colonnes,
																  		'hash' => 'entrees')) ?>

				<?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Total sorties',
																	   'unite' => 'hl',
	    						  								       'counter' => 4,
																	   'colonnes' => $colonnes,
																	   'cssclass_libelle' => 'total',
																	   'cssclass_value' => 'total',
																	   'hash' => 'total_sorties')) ?>

				<?php include_partial('drm_export/pdfLineDetail', array('certification_config' => $certification->getConfig(),
	    						  								        'counter' => 4,
																		'colonnes' => $colonnes,
																  		'hash' => 'sorties')) ?>

				<?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Total fin de mois',
																	   'unite' => 'hl',
	    						  								       'counter' => 5,
																	   'cssclass_libelle' => 'total',
																  	   'cssclass_value' => 'total',
																  	   'colonnes' => $colonnes,
																  	   'hash' => 'total')) ?>

				<?php include_partial('drm_export/pdfLineDetail', array('certification_config' => $certification->getConfig(),
	    						  								        'counter' => 5,
																		'colonnes' => $colonnes,
																  		'hash' => 'stocks_fin')) ?>

			</table>
		<?php endif; ?>

		<?php $colonnes = $pagers_vrac[$certification_key]->getResults(); ?>
		<?php if(count($colonnes) > 0): ?>
			<h2>Contrats vrac - <?php echo $certification->getConfig()->libelle ?></h2>
			<table class="recap volumes bloc_bottom">
				<?php include_partial('drm_export/pdfLine', array('libelle' => 'Code produit',
																  'colonnes' => $colonnes,
																  'cssclass_value' => 'libelle',
																  'partial' => 'drm_export/pdfLineVracItemProduitLibelle')) ?>

				<?php include_partial('drm_export/pdfLine', array('libelle' => 'N° de contrat', 
																	   'colonnes' => $colonnes,
																	   'cssclass_libelle' => 'detail',
																  	   'cssclass_value' => 'detail',
																  	   'method' => 'getKey')) ?>

				<?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Volume',
																  	   'colonnes' => $colonnes,
																  	   'unite' => 'hl',
																  	   'cssclass_libelle' => 'detail',
																  	   'cssclass_value' => 'detail',
																  	   'hash' => 'volume')) ?>


				
			</table>
		<?php endif; ?>

		<?php $pagers_volume[$certification_key]->gotoNextPage(); ?>
		<?php $pagers_vrac[$certification_key]->gotoNextPage(); ?>
		<hr />
		<?php endwhile; ?>
	<?php endforeach; ?>
	
	
		
	<?php if($drm->declaration->hasMouvement() && !$drm->declaration->hasStockEpuise()): ?>
	<?php else: ?>
		<div class="bloc_bottom">
			<h2>Suivi des vins</h2>
			<?php if($drm->declaration->hasPasDeMouvement()): ?>
	            <p>Pas de mouvement pour l'ensemble des produits</p>
	        <?php endif; ?>
	        <?php if($drm->declaration->hasStockEpuise()): ?>
	            <p>Stock épuisé pour l'ensemble des produits</p>
	        <?php endif; ?>
    	</div>
	<?php endif; ?>

	<table class="double_col bloc_bottom">
		<tr>
			<td class="col_left">
				<h2>Mouvements au cours du mois</h2>
				<p><strong>Documents prévalidés ou N° empreinte utilisés au cours du mois</strong></p>
				<p>
					<strong>DAA</strong> 
					<?php if($drm->declaratif->daa->debut > 0 || $drm->declaratif->daa->debut > 0): ?>
					du <?php echo $drm->declaratif->daa->debut ?> au <?php echo $drm->declaratif->daa->fin ?>
					<?php else: ?>
					Aucun
					<?php endif; ?>
				</p>
				<p>
					<strong>DSA</strong>
					<?php if($drm->declaratif->dsa->debut > 0 || $drm->declaratif->dsa->debut > 0): ?>
					du <?php echo $drm->declaratif->dsa->debut ?> au <?php echo $drm->declaratif->dsa->fin ?>
					<?php else: ?>
					Aucun
					<?php endif; ?>
				</p>
				<?php if($drm->declaratif->adhesion_emcs_gamma): ?>
					<p>
						<strong>Adhère</strong> à EMCS / GAMMA (n° non nécessaires)
					</p>
				<?php endif; ?>
			</td>
			<td class="col_right">
				<h2>Défaut d'apurement</h2>
				<p class="bloc_bottom">
					<?php if($drm->declaratif->defaut_apurement): ?>
						Défaut d'apurement à déclarer (Joindre relevé de non apurement et copie du DAA)
					<?php else: ?>
						Pas de défaut d'apurement
					<?php endif; ?>		
					
				</p>
				<h2>Caution</h2>
				<p>
					<?php if($drm->declaratif->caution->dispense): ?>
						Dispensé
					<?php else: ?>
						Oui, Organisme : <?php echo $drm->declaratif->caution->organisme ?>
					<?php endif; ?>
				</p>
			</td>
		</tr>
	</table>

	
<?php if ($drm->valide->date_saisie): ?>
	<?php while($pager_droits_douane->getPage() <= $pager_droits_douane->getLastPage()): ?>
		<?php $colonnes = $pager_droits_douane->getResults(); ?>
		<h2>Droits de circulation et de consommation</h2>
		<table class="recap droits_douane bloc_bottom">
	    <?php include_partial('drm_export/pdfLine', array('libelle' => '',
	    						  'counter' => '&nbsp;',
	    						  'cssclass_counter' => 'counterNone',
							      'colonnes' => $colonnes,
							      'cssclass_libelle' => 'vide',
							      'cssclass_value' => 'libelle',
							      'partial_cssclass_value' => 'drm_export/pdfLineDroitsDouaneItemIsTotalCss',
							      'method' => 'getLibelle')) ?>
	    
	    <?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Volume réintégré',
	    						   'counter' => 'a',
								   'colonnes' => $colonnes,
								   'unite' => 'hl',
								   'cssclass_libelle' => 'detail',
								   'partial_cssclass_value' => 'drm_export/pdfLineDroitsDouaneItemIsTotalCss',
								   'hash' => 'volume_reintegre')) ?>
	    
	    <?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Volume taxé',
	    						   'counter' => 'b',
								   'colonnes' => $colonnes,
								   'unite' => 'hl',
								   'cssclass_libelle' => 'detail',
								   'partial_cssclass_value' => 'drm_export/pdfLineDroitsDouaneItemIsTotalCss',
								   'hash' => 'volume_taxe')) ?>
	    
	    <?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Taux des droits en vigueur',
	    						   'counter' => 'c',
								   'colonnes' => $colonnes,
								   'unite' => '€/hl',
								   'cssclass_libelle' => 'detail',
								   'partial_cssclass_value' => 'drm_export/pdfLineDroitsDouaneItemIsTotalCss',
								   'hash' => 'taux')) ?>
	    
	    <?php include_partial('drm_export/pdfLineFloat', array('libelle' => 'Droits à payer',
	    						   'counter' => 'd',
								   'colonnes' => $colonnes,
								   'unite' => '€',
								   'cssclass_libelle' => 'total',
								   'cssclass_value' => 'total',
								   'hash' => 'payable')) ?>

	    <?php if ($drm->isPaiementAnnualise()): ?>
			<?php include_partial('drm_export/pdfLine', array('libelle' => 'Report du mois précédent',
	    						   								   'counter' => 'e',
																   'colonnes' => $colonnes,
																   'cssclass_libelle' => 'total',
																   'cssclass_value' => 'total',
																   'partial' => 'drm_export/pdfLineReport')) ?>
			<?php include_partial('drm_export/pdfLine', array('libelle' => 'Total cumulé à reporter ou à solder',
	    						   								   'counter' => 'f',
																   'colonnes' => $colonnes,
																   'cssclass_libelle' => 'total',
																   'cssclass_value' => 'total',
																   'partial' => 'drm_export/pdfLineCumul')) ?>
		<?php endif; ?>  
		</table>
		<?php $pager_droits_douane->gotoNextPage(); ?>
	<?php endwhile; ?>
	<div class="bloc_bottom">
		<h2>Paiement des droits de circulation</h2>
		<p><strong>Echéance de paiement</strong> : <?php echo $drm->declaratif->paiement->douane->frequence ?></p>
		<p><strong>Mode de paiement</strong> : <?php echo $drm->declaratif->paiement->douane->moyen ?></p>
	</div>

	<table class="double_col bloc_bottom">
		<tr>
			<td class="col_left">
				<h2>Cadre reservé à l'administration des douanes</h2>
				<p>Date de récéption / Cachet dateur : <br /><br/><br/>
				<p>N° de déclaration GILDA : <br /><br/><br/></p>
			</td>
			<td class="col_right">
				<h2>Déclaration établie </h2>
				<p><strong>le : <?php echo $drm->getEuValideDate(); ?></strong></p>
				<p>via l'application Déclarvin</p>
			</td>
		</tr>
	</table>

<?php endif; //si pas validée ?>
	<hr />
	<div class="legende">
	<?php foreach($drm->declaration->certifications as $certification_key => $certification): ?>
		<?php $i = 1; while($pagers_code[$certification_key]->getPage() <= $pagers_code[$certification_key]->getLastPage()): ?>
		<?php $colonnes = $pagers_code[$certification_key]->getResults(); ?>
		<?php if(count($colonnes) > 0): ?>
			<h2>Codes produit - <?php echo $certification->getConfig()->libelle ?></h2>
			<table>
				<?php $counter=0; foreach($colonnes as $key => $item): ?>
					<?php if($item): ?>
					<?php if ($counter == 0): ?>
					<tr>
					<?php elseif ($counter == ExportDRM::NB_COL_CODES): $counter = 0;?>
					</tr>
					<tr>
					<?php endif; ?>
					<td>
						<strong><?php echo strtoupper($item->getCode()) ?></strong>
			   			<span><?php echo $item->getConfig()->getLibelleFormat(array(), "%format_libelle%") ?></span>
					</td>
					<?php endif; ?>
				<?php $counter++; endforeach; ?>
				</tr>
			</table>
			<?php if ($pagers_code[$certification_key]->getPage() != $pagers_code[$certification_key]->getLastPage()): ?>
			<hr />
			<?php endif; ?>
		<?php endif; $i++; ?>
		<?php $pagers_code[$certification_key]->gotoNextPage(); ?>
		<?php endwhile; ?>
	<?php endforeach; ?>
	</div>
</body>
</html>
