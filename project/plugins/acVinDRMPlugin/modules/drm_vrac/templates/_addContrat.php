<tr>
	<td><?php echo $detail->getLibelle(ESC_RAW); ?></td>
	<td align="center" colspan="2">
		<?php if ($detail->hasContratVrac()): ?>
                    <div class="btn">
						<a href="<?php echo url_for('drm_vrac_ajout_contrat', $detail) ?>" class="btn_ajouter btn_popup" data-popup-ajax="true" data-popup="#popup_ajout_contrat_<?php echo $detail->getIdentifiantHTML() ?>" data-popup-config="configForm"></a>
					</div>
		<?php endif; ?>
	</td>
</tr>