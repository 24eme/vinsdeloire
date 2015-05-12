<?php use_helper('Float'); ?>
<?php if($item->isTotal()): ?>
	<span class="zero"><?php echoFloatFr($item->report) ?> <span class="unite">€</span></span>
<?php else: ?>
	&nbsp;
<?php endif; ?> 