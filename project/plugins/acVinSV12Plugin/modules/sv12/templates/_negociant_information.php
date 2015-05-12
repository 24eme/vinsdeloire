<div id="negociant_infos" class="bloc_form">    
	<div class="ligne_form">
		<span><label>Négociant :</label> <?php echo $etablissement->identifiant; ?></span>
	</div>
	<div class="ligne_form ligne_form_alt">
		<span><label>CVI :</label> <?php echo $etablissement->cvi; ?></span>
	</div>
	<div class="ligne_form">
		<span><label>Commune :</label> <?php echo $etablissement->siege->commune; ?></span>
	</div>
</div>