<?php
use_helper('Display');
?>
<div id="ds_infos" class="bloc_form">   
	<div class="ligne_form">
		<span>
			  <label>Numéro d'archive :</label>
			  <?php display_field($ds,'numero_archive'); ?>
		</span>
	</div> 
	<div class="ligne_form ligne_form_alt">
		<span>
			  <label>N° DS :</label>
			  <?php display_field($ds,'_id'); ?>
		</span>
	</div>
	<div class="ligne_form">
		<span>
			<label>Campagne viticole : </label>
			<?php display_field($ds,'campagne'); ?>
		</span>
	</DIV>
<?php if (isset($ds->key[DSHistoryView::KEY_STATUT])) : ?>
	<div class="ligne_form ligne_form_alt">
		<span>
			<label>Etat : </label>
                <?php if ($ds->key[DSHistoryView::KEY_STATUT] == DSClient::STATUT_A_SAISIR) echo "A saisir"; else echo "Validée"; ?>
		</span>
	</div>
<?php endif; ?>
</div>