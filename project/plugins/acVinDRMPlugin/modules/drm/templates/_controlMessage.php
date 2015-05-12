<?php if ($sf_user->hasFlash('control_message')): ?>
    <div class="flash_control flash_temporaire <?php echo $sf_user->getFlash('control_css'); ?>">
	    <?php echo $sf_user->getFlash('control_message'); ?></p>
    </div>
<?php endif; ?>