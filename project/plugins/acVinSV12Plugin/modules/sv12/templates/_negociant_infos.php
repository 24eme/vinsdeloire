<ul id="recap_infos_header">
    <li>
        <span>Campagne viticole :</span>
        <?php echo $sv12->campagne ?>
    </li>
</ul>

<div class="bloc_form">
	<div class="ligne_form">
		<span><label>Numéro d'archive :</label>  <?php echo $sv12->numero_archive ?></span>
	</div>
	<div class="ligne_form ligne_form_alt">
		<span><label>Négociant :</label>  <?php echo $sv12->declarant->nom; ?></span>
	</div>
	<div class="ligne_form ">
		<span><label>CVI :</label> <?php echo $sv12->declarant->cvi; ?></span>
	</div>
	<div class="ligne_form ligne_form_alt">
		<span><label>Commune :</label>  <?php echo $sv12->declarant->commune; ?></span>
	</div>
</div>