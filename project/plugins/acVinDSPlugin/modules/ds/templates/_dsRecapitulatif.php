<?php
use_helper('Float');
?>
<form action="<?php echo url_for('ds_edition_operateur_validation_visualisation', $ds); ?>" method="post" id="ds_edition_validation_form">
 <fieldset id="dsRecapitulatif">
	<table id="ds_recapitulatif_table" class="table_recap">
	<thead>
		<tr>
			<th>Produits</th>
            <th>
                <?php if($ds->drm_origine): ?>
                (DRM <?php echo preg_replace ('/.*-(\d{4})(\d{2})$/', '\2/\1', $ds->drm_origine); ?>)
                <?php else: ?>
                (Aucune DRM)
                <?php endif; ?>
            </th>
			<th>Volume déclaré</th>
            <th>VCI</th>
            <th>Réserve qual.</th>
		</tr>
	</thead>
	<tbody class="ds_recapitulatif_tableBody">
		<?php foreach ($ds->declarations as $declaration) :
		?>
		<tr id="ds_declaration_recapitulatif">
			<td class="ds_recap_declaration_appelation">
				<?php echo $declaration->produit_libelle; ?>
			</td>
			<td class="ds_recap_declaration_stockInitial">
                <?php if(!is_null($declaration->stock_initial)): ?>
                 (<?php echoFloat($declaration->stock_initial); ?>)
                <?php endif; ?>
			</td>
			<td class="ds_recap_declaration_vr">
				<?php echoFloat($declaration->stock_declare); ?>
			</td>
                        <td class="ds_recap_declaration_vci">
				<?php echo $declaration->vci; ?>
			</td>
                        <td class="ds_recap_declaration_reserve_qualitative">
				<?php echo $declaration->reserve_qualitative; ?>
			</td>
		  </tr>
  <?php if ($declaration->hasElaboration() && $declaration->exist('stock_elaboration')): ?>
		<tr id="ds_declaration_recapitulatif">
			<td class="ds_recap_declaration_appelation">
				<?php echo $declaration->produit_libelle; ?> - en élaboration
			</td>
            <td>&nbsp;</td>
			<td class="ds_recap_declaration_vr">
				<?php echoFloat($declaration->stock_elaboration); ?>
			</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
		  </tr>
  <?php endif; ?>
		<?php
		endforeach;
		?>
	</tbody>
	</table> 
</fieldset>

<div id="ds_recapitulatif_commentaires">
    <h3>Commentaires&nbsp;: </h3>
	<p><pre class="commentaire"><?php echo $ds->commentaire; ?></pre></p>
</div>

<div class="btn_etape">
	<a href="<?php echo url_for('ds_edition_operateur', $ds); ?>" class="btn_majeur btn_modifier"><span>Modifier</span></a>
	<button type="submit" id="ds_edition_validation" class="btn_majeur btn_valider ds_declaration_validation">Terminer</button>
</div>
    
</form>
