<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">
    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'ajouts_liquidations', 'pourcentage' => '10')); ?>

    <section id="principal">
		<div id="application_dr">
			<div id="contenu_onglet">
				<?php if($first_certification): ?>
                                <form method="post" action="<?php echo url_for('drm_mouvements_generaux', $drm) ?>">
                                    <button class="btn_passer_etape" type="submit">Passer cette étape</button>
                                </form>
				<?php endif; ?>
				<p class="intro">Au cours du mois écoulé, avez-vous connu des changements de structure particuliers ?</p>
        		
        		<div id="ajouts_liquidations">
        			<?php if ($drm->hasDetails()): ?>
        			<?php if(!$drm->declaration->hasMouvement()): ?>
        			<div id="form" style="padding:15px 0 0 0; margin:0 0 15px 0;" class="tableau_ajouts_liquidations">
        				<form class="updateProduct" action="<?php echo url_for('drm_mouvements_generaux_produits_update', $drm) ?>" method="post">
		        			<table class="tableau_recap">
								<tbody>
								<tr class="alt">
									<td style="width: 694px;" colspan="2">
										<label for="<?php echo $form['pas_de_mouvement']->renderId() ?>">Pas de mouvement pour l'ensemble des produits <a href="" class="msg_aide" data-msg="help_popup_mouvgen_pasdemouvement_global" title="Message aide"></a>
										</label>
									</td>
									<td style="width: 140px" align="center">
										<?php echo $form->renderHiddenFields() ?>
										<?php echo $form['pas_de_mouvement']->render() ?>
									</td>
								</tr>
								</tbody>
							</table>
						</form>
	        		</div>
	        		<?php endif; ?>
        			
        			<?php if($drm->declaration->hasStockEpuise()): ?>
        			<a href="<?php echo url_for('drm_mouvements_generaux_stock_epuise', $drm) ?>" id="stock_epuise" style="float:none; margin: 0 0 15px 0;">Stock épuisé</a>
	        		<?php endif; ?>
	        		<?php endif; ?>
        			<?php foreach ($forms as $certification => $tabForm): ?>
		            <div id="tableau_<?php echo strtolower($certificationLibelle[$certification]) ?>" class="tableau_ajouts_liquidations">
		                    <h2><?php echo $certificationLibelle[$certification] ?></h2>
		                    <div class="recap_produit">
			                    <table class="tableau_recap">
			                        <thead>
										<tr>
											<th style="width: 570px">Produits</th>
											<th style="width: 170px">Stock début de mois <a href="" class="msg_aide" data-msg="help_popup_mouvgen_disponible" title="Message aide"></a></th>
											<th style="width: 150px">Pas de mouvement <a href="" class="msg_aide" data-msg="help_popup_mouvgen_pasdemouvement" title="Message aide"></a></th>
										</tr>
									</thead>
									<tbody>
			                        <?php
			                        $hasItem = 0;
			                        if ($tabForm):
			                            foreach ($tabForm as $form): $hasItem++ ?>
			                                <?php include_partial('item', array('form' => $form)) ?>
			                            <?php endforeach;
			                        endif;
			                        ?>
			                        </tbody>
			                    </table>
			                    <div class="btn">
			                    	<?php if ($certifs[$certification]): ?>
				                    	<?php if (!$hasItem): ?>
				                    	<a href="<?php echo url_for('drm_mouvements_generaux_product_add', $drm->declaration->certifications->add($certification)) ?>" class="btn_ajouter">Ajouter un nouveau produit</a>
				                    	<?php endif; ?>
			                    	<?php else: ?>
									<a href="<?php echo url_for('drm_mouvements_generaux_product_ajout', $drm->declaration->certifications->add($certification)) ?>" class="btn_ajouter btn_popup" data-popup="#popup_ajout_produit_<?php echo $certification ?>" data-popup-config="configForm">Ajouter un nouveau produit</a>
									<?php endif; ?>
								</div>
		                    </div>
		            </div>
		            <?php endforeach; ?>
            	</div>
        	</div>
	        <div id="btn_etape_dr">
	            <a href="<?php echo url_for('drm_informations', $drm) ?>" class="btn_prec">
	            	<span>Précédent</span>
	            </a>
                <?php if($first_certification): ?>
	            <form action="<?php echo url_for('drm_mouvements_generaux', $drm) ?>" method="post">
	            	<button type="submit" class="btn_suiv"><span>Suivant</span></button>
	            </form>
                <?php endif; ?>
	        </div>
		</div>
    </section>
</section>

<script type="text/javascript">
	$(document).ready(function () {
		$("#produits_pas_de_mouvement").change(function() {
			if ($("#produits_pas_de_mouvement:checked").length > 0) {
				$("#ajouts_liquidations input.pas_de_mouvement[type=checkbox]").attr('checked', 'checked');
			} else {
				$("#ajouts_liquidations input.pas_de_mouvement[type=checkbox]").removeAttr('checked');
			}
		});

		$("#ajouts_liquidations input.pas_de_mouvement[type=checkbox]").click(function() {
			if ($("#ajouts_liquidations input.pas_de_mouvement[type=checkbox]:checked").length == $("#ajouts_liquidations input.pas_de_mouvement[type=checkbox]").length) {
				$("#produits_pas_de_mouvement").attr('checked', 'checked');
			} else {
				$("#produits_pas_de_mouvement").removeAttr('checked');
			}
		});
	});
</script>