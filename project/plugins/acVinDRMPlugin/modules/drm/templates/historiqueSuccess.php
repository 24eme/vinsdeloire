<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">
    <h1>Historique de vos Déclarations Récapitulatives Mensuelles</h1>
    <section id="principal">
	    <div id="recap_drm">
			<div id="drm_annee_courante">
				<?php include_component('drm', 'historiqueList', array('historique' => $historique)) ?>
			</div>
		</div>
		
		<ul id="nav_drm_annees">
			<?php foreach ($historique->getCampagnes() as $campagne): ?>
				<?php if ($campagne == $historique->getCampagneCourante()): ?>
					<li class="actif"><strong>DRM <?php echo $campagne ?></strong></li>
				<?php else: ?>
					<li><a href="<?php echo url_for('drm_historique', array('campagne' => $campagne, 'identifiant'=> $historique->getIdentifiant()))?>">DRM <?php echo $campagne ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</section>
</section>
