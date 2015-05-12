<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="contenu">
	<form action="<?php echo url_for('vrac_export_etiquette'); ?>" method="POST">
		<?php
		echo $etiquettesForm->renderHiddenFields();
		echo $etiquettesForm->renderGlobalErrors();
		?>
	
		<ul>
                    <li id="date_debut" class="ligne_form champ_datepicker">                        
				<?php echo $etiquettesForm['date_debut']->renderError(); ?>
				<?php echo $etiquettesForm['date_debut']->renderLabel() ?>
				<?php echo $etiquettesForm['date_debut']->render() ?>
                    </li>
                     <li id="date_fin" class="ligne_form champ_datepicker">       
				<?php echo $etiquettesForm['date_fin']->renderError(); ?>
				<?php echo $etiquettesForm['date_fin']->renderLabel() ?>
				<?php echo $etiquettesForm['date_fin']->render() ?> 
                     </li>
		</ul>
		
		<div class="btn_form">
			<button type="submit" id="alerte_valid" class="btn_majeur btn_valider">Export CSV</button>
		</div>
	</form>
</div>