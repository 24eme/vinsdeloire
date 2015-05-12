<div id="statut_declaration">
	<nav id="declaration_etapes">
		<ol>
                  <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => 'Informations',
                                                               'numero' => 1,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_informations', $drm),
                                                               'cssclass' => 'premier')); ?>

                  <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => 'Ajouts / Liquidations',
                                                               'numero' => 2,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_mouvements_generaux', $drm),
                                                               'cssclass' => null)); ?>
            <?php foreach($certifications as $key => $certification): ?>
                 <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => $certification->getConfig()->getLibelle(),
                                                               'numero' => $key,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_recap', $certification),
                                                               'cssclass' => null)); ?>
            <?php endforeach; ?>
            <?php if ($numero_vrac): ?>
            <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => 'Vrac',
                                                               'numero' => $numero_vrac,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_vrac', $drm),
                                                               'cssclass' => null)); ?>
            <?php endif; ?>

            <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => 'Déclaratif',
                                                               'numero' => $numero_declaratif,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_declaratif', $drm),
                                                               'cssclass' => null)); ?>

            <?php include_partial('drm/etapeItem', array('drm' => $drm,
                                                               'libelle' => 'Validation',
                                                               'numero' => $numero_validation,
                                                               'numero_courant' => $numero,
                                                               'numero_autorise' => $numero_autorise,
                                                               'url' => url_for('drm_validation', $drm),
                                                               'cssclass' => 'dernier')); ?>
		</ol>
	</nav>	
	<div id="etat_avancement">
		<p>Vous avez saisi <strong><?php echo $pourcentage ?><span>%</span></strong></p>
		<div id="barre_avancement">
			<div style="width: <?php echo $pourcentage ?>%"></div>
		</div>
	</div>
</div>