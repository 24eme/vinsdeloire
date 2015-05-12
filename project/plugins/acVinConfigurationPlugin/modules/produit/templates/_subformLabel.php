<div class="form_ligne" data-key="<?php echo $form->getName() ?>">
	<table>
		<tr>
			<td><span class="error"><?php echo $form['label']->renderError() ?></span><?php echo $form['label']->renderLabel() ?><br /><?php echo $form['label']->render() ?></td>
			<td><span class="error"><?php echo $form['code']->renderError() ?></span><?php echo $form['code']->renderLabel() ?><br /><?php echo $form['code']->render() ?></td>
			<td><br />&nbsp;<a href="javascript:void(0)" class="removeForm btn_suppr"></a></td>
		</tr>
	</table>
</div>