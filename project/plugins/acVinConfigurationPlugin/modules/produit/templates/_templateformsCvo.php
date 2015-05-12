<script id="templateformsCvo" type="text/x-jquery-tmpl">
<div class="ligne_form" data-key="${index}">
	<table>
		<tbody>
			<tr>
				<td>
					<span class="error"></span>
					<label for="produit_definition_droit_cvo_${index}_date">Date: </label>
					<br>
					<input id="produit_definition_droit_cvo_${index}_date" class="datepicker" type="text" name="produit_definition[droit_cvo][${index}][date]">
				</td>
				<td style="padding-left: 10px;">
					<span class="error"></span>
					<label for="produit_definition_droit_cvo_${index}_taux">Taux: </label>
					<br>
					<input type="text" id="produit_definition_droit_cvo_${index}_taux" class=" num num_float" autocomplete="off" name="produit_definition[droit_cvo][${index}][taux]">
				</td>
				<td>
					<br>
					&nbsp;<a class="removeForm btn_suppr" href="#"></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</script>