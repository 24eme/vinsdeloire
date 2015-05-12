<?php use_helper('Date') ?>

<h1>Déclaration Récapitulative Mensuelle</h1>
<p id="date_drm">
	DRM de <?php echo format_date($drm->getAnnee().'-'.$drm->getMois().'-01', 'MMMM yyyy', 'fr_FR') ?>
	<?php if($drm->isRectificative()): ?>
		- <strong style="color: #ff0000; text-transform: uppercase;">
			Rectificative n° <?php echo sprintf('%02d', $drm->rectificative) ?>
		</strong>
	<?php endif; ?>
</p>
