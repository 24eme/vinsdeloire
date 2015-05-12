<script id="templateformsLabel" type="text/x-jquery-tmpl">
<div data-key="${index}" class="form_ligne">
	<table>
		<tbody>
			<tr>
			<td><span class="error"></span><label for="produit_definition_labels_${index}_label">Label: </label><br><input type="text" id="produit_definition_labels_${index}_label" name="produit_definition[labels][${index}][label]"></td>
			<td><span class="error"></span><label for="produit_definition_labels_${index}_code">Code: </label><br><input type="text" id="produit_definition_labels_${index}_code" name="produit_definition[labels][${index}][code]"></td>
			<td><br />&nbsp;<a class="removeForm btn_suppr" href="#"></a></td>
			</tr>
		</tbody>
	</table>
</div>
</script>