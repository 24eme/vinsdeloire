<div class="form_ligne" data-key="<?php echo $form->getName() ?>">
	<?php if($form['departement']->hasError()){ ?> <span class="error"><?php echo $form['departement']->renderError() ?></span> <?php } ?>
	<?php echo $form['departement']->render() ?>
	&nbsp;<a href="javascript:void(0)" class="removeForm btn_suppr"></a>
</div>