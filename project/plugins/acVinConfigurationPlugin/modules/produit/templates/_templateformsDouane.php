<script id="templateformsDouane" type="text/x-jquery-tmpl">
<div class="ligne_form" data-key="${index}">
	<table>
		<tbody>
			<tr>
				<td>
					<span class="error"></span>
					<label for="produit_definition_droit_douane_${index}_date">Date: </label>
					<br>
					<input id="produit_definition_droit_douane_${index}_date" class="datepicker" type="text" name="produit_definition[droit_douane][${index}][date]">
				</td>
				<td style="padding-left: 10px;">
					<span class="error"></span>
					<label for="produit_definition_droit_douane_${index}_taux">Taux: </label>
					<br>
					<input type="text" id="produit_definition_droit_douane_${index}_taux" class=" num num_float" autocomplete="off" name="produit_definition[droit_douane][${index}][taux]">
				</td>
				<td>
			</tr>
		</tbody>
	</table>
</div>
</script>