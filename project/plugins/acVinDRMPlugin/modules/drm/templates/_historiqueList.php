<table>
	<thead>
		<tr>
			<th>DRM</th>
			<th>Etat <a href="" class="msg_aide" data-msg="help_popup_monespace_etat" title="Message aide"></a></th>
            <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
                 <th>Mode de saisie</th>
            <?php endif; ?>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($hasNewDRM): ?>
		<tr class="alt">
			      <td><?php echo $futurDRM[DRMHistorique::VIEW_INDEX_ANNEE].'-'.$futurDRM[DRMHistorique::VIEW_INDEX_MOIS] ?></td>
			<td>NOUVELLE</td>
			<td>
				<a href="<?php echo url_for('drm_nouvelle', array('identifiant' => $historique->geEtablissementIdentifiant(), 'periode' => $futurDRM[DRMHistorique::VIEW_INDEX_ANNEE].'-'.$futurDRM[DRMHistorique::VIEW_INDEX_MOIS])) ?>">Démarrer la DRM</a><br />
			</td>
		</tr>
		<?php endif; ?>
		<?php $i=0; foreach ($list as $drm_id => $drm): ?>
		<?php include_component('drm', 'historiqueItem', array('alt' => $i%2 == 0, 'drm' => $drm)) ?>
		<?php $i++; if (isset($limit) && $limit == $i) break; endforeach; ?>
	</tbody>
</table>
