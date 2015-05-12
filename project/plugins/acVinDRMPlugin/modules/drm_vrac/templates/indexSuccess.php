<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'vrac', 'pourcentage' => '30')); ?>

    <section id="principal">
		<div id="application_dr">
			<div id="contenu_onglet">
				<?php if ($details->count() > 0): ?>
				<table width="100%">
					<thead>
						<tr>
							<th align="left">Produit</th>
							<th width="300px">Contrat</th>
							<th width="200px">Volume</th>
							<th width="50px"></th>
						</tr>
					</thead>
					<tbody>
				   <?php 
				   foreach ($details as $detail) {
      
      if (isset($noContrats[$detail->getIdentifiantHTML()]) && $noContrats[$detail->getIdentifiantHTML()]) {
	echo '<tr><td>'.$detail->getLibelle()->getRawValue().'</td><td>Pas de contrat défini pour ce produit.<br/>Merci de contacter votre interpro</td><td></td></tr>';

      } else {
      	include_partial('addContrat', array('detail' => $detail));
      }
      if (isset($forms[$detail->getIdentifiantHTML()])) { 
	foreach ($forms[$detail->getIdentifiantHTML()] as $form) {
	  include_partial('itemContrat', array('form' => $form));
	}
      }
    }
?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<div id="btn_etape_dr">
	            <a href="<?php echo url_for('drm_recap_redirect', $drm) ?>" class="btn_prec">
	            	<span>Précédent</span>
	            </a>
    <?php if (!count($noContrats)) : ?>
	            <form action="<?php echo url_for('drm_vrac', $drm) ?>" method="post">
	            	<button type="submit" class="btn_suiv"><span>Suivant</span></button>
	            </form>
    <?php endif; ?>
	        </div>
		</div>
	</section>
</section>