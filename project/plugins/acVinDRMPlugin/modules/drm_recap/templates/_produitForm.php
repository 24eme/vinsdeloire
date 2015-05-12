<form id="form_produit_declaration" method="post" action="<?php echo url_for("drm_produit_ajout", $drm) ?>">
	<?php echo $form ?>
</form>
<br />

<script type="text/javascript">
$(document).ready(function() {
	$('#produit_declaration_hashref').change(function() {
		$('#form_produit_declaration').submit();
	});
	$('#form_produit_declaration').submit(function() {
		$.post($(this).attr('action'), $(this).serializeArray(), function (data) {
			if (data.success) {
				$('#col_saisies_cont').append(data.content);
			}
		}, 'json');
		return false;
	});
});
</script>