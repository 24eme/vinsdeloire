<tr>
	<?php foreach($colonnes as $col_key => $item): ?>
	<?php if(!is_null($item)): ?>
	<td>
		<?php echo $item ?>
	</td>
	<?php else: ?>
	<td class="vide">&nbsp;</td>
	<?php endif; ?>
	<?php endforeach; ?>
</tr>