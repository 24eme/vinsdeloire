<tr>
	<?php if (isset($counter)): ?>
	<td class="counter<?php if (isset($cssclass_counter)): ?> <?php echo $cssclass_counter ?><?php endif; ?>"><?php echo $counter ?></td>
	<?php endif; ?>
	<th class="<?php echo ((isset($cssclass_libelle)) ? $cssclass_libelle : null) ?>"><?php echo $libelle ?></th>
	<?php foreach($colonnes as $col_key => $item): ?>
	<?php if(!is_null($item)): ?>
	<?php $td_cssclass_ = ((isset($cssclass_value)) ? $cssclass_value : null) ?>
	<?php $td_cssclass_ .= ((isset($partial_cssclass_value)) ? ' '.get_partial($partial_cssclass_value, array('item' => $item, 'hash' => isset($hash) ? $hash : null)) : null) ?>
	<td class="<?php echo $td_cssclass_ ?>">
	    <?php if(isset($partial)): ?>
	    <?php include_partial($partial, array_merge(array('item' => $item, 
							      'hash' => isset($hash) ? $hash : null),
							isset($partial_params) && is_array($partial_params->getRawValue()) ? $partial_params->getRawValue() : array())) ?>
		<?php elseif(isset($method)): ?>
		<?php echo call_user_func_array(array($item, $method), array()) ?>
		<?php elseif($item instanceof acCouchdbJson): ?>
			<?php echo $item->get($hash) ?>
		<?php elseif(is_array($item)): ?>
			<?php echo $item[$hash] ?>
		<?php else: ?>
			<?php echo $item ?>
		<?php endif; ?>
	</td>
	<?php else: ?>
	<td class="vide">&nbsp;</td>
	<?php endif; ?>
	<?php endforeach; ?>
</tr>