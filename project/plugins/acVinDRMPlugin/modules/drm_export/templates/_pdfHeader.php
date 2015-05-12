<div id="header">
   <center>
<h1>
<?php if (!$drm->valide->date_saisie) {
   echo '<span class="rectificative">Brouillon&nbsp;: </span> ';
 }?>Déclaration récapitulative mensuelle en droits suspendus de <?php echo $drm->getHumanDate(); ?> 
<?php if($drm->isRectificative()): ?>
 - <span class="rectificative">Rectificative <?php echo sprintf('%02d', $drm->rectificative) ?></span>
<?php endif; ?>
<br/>Vins de la vallées du Rhône et de Provence (Inter-Rhône, CIVP, InterVins SE)
</h1></center>
<table>
<tr>
	<td class="premier"><strong>Nom / Raison sociale : <?php echo $drm->declarant->nom ?></strong></td>
   <td>n° DRM : <?php echo preg_replace('/DRM-/', '', $drm->_id); ?> (validée le <?php echo $drm->getEuValideDate(); ?>)</td>
</tr>
<tr>
	<td class="premier" >Lieu où est tenue la comptabilité matière :
		<?php if ($drm->declarant->comptabilite->adresse): ?>
		<?php echo $drm->declarant->comptabilite->adresse ?>, <?php echo $drm->declarant->comptabilite->code_postal ?> <?php echo $drm->declarant->comptabilite->commune ?>
		<?php else: ?>
		<?php echo $drm->declarant->siege->adresse ?>, <?php echo $drm->declarant->siege->code_postal ?> <?php echo $drm->declarant->siege->commune ?>
		<?php endif; ?>
	</td>
	<td>
		<?php if($drm->declarant->siret): ?>
			SIRET : <?php echo $drm->declarant->siret ?>
		<?php elseif($drm->declarant->cni): ?>
			CNI : <?php echo $drm->declarant->cni ?>
		<?php else: ?>
			SIRET :
		<?php endif; ?>
	</td>
</tr>
<tr>
	<td class="premier">Adresse et n° d'EA du chai : 
		<?php echo $drm->declarant->siege->adresse ?>, <?php echo $drm->declarant->siege->code_postal ?> <?php echo $drm->declarant->siege->commune ?>
	</td>
	<td>Accises : <?php echo $drm->declarant->no_accises ?></td>
</tr>
<tr>
	<td class="premier">Service des douanes de : Orange</td>
	<td>CVI : <?php echo $drm->declarant->cvi ?></td>
</tr>
</table>
</div>
