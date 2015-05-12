<div class="error_list">
    <h3>Points bloquants</h3>
    <ol>
        <?php foreach ($drmValidation->getErrors() as $identifiant => $error): ?>
            <li>
            	<a href="<?php echo ($error->getLien() != DRMValidation::NO_LINK) ? url_for('drm_show_error', array('sf_subject' => $drm,
				            										'type' => $error->getType(),
				            										'id' => $identifiant)) : DRMValidation::NO_LINK; ?>">
					<?php echo $error->getMessage(); ?>
            	</a>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
<br />