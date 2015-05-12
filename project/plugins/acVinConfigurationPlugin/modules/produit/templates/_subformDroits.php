<div class="ligne_form" data-key="<?php echo $form->getName() ?>">
	<table>
		<tr>
			<td><span class="error"><?php echo $form['date']->renderError() ?></span><?php echo $form['date']->renderLabel() ?><br /><?php echo $form['date']->render() ?></td>
			<td style="padding-left: 10px;"><span class="error"><?php echo $form['taux']->renderError() ?></span><?php echo $form['taux']->renderLabel() ?><br /><?php echo $form['taux']->render() ?></td>
		</tr>
	</table>
</div>
