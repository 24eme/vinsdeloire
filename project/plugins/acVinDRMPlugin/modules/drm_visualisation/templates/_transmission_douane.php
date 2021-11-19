<div id="contenu_onglet">
    <h2>Transmission Douane</h2>
    <table class="table_recap">
        <thead>
            <tr>
                <th>Transmission sur le portail proDou@ne <?php if (!$isTeledeclarationMode || $sf_user->isUsurpationCompte()) : echo "("; if ($drm->exist('transmission_douane') && !is_null($drm->transmission_douane->xml)) : ?><a href="<?php echo url_for('drm_xml_table', array("identifiant" => $drm->identifiant,"periode_version" => $drm->getPeriodeAndVersion(), "retour" => "0")); ?>">XML transmis</a> - <?php endif; ?><a href="<?php echo url_for('drm_edition_libelles', array('identifiant' => $drm->identifiant,"periode_version" => $drm->getPeriodeAndVersion())); ?>">Edition libellés</a>)<?php endif; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr><td>
<?php if ($drm->exist('transmission_douane') && ($drm->transmission_douane->xml != null)):
    if ($drm->transmission_douane->success) : ?>
La transmission a été réalisée avec succès le <?php echo $drm->getTransmissionDate(); ?> : accusé reception numéro <?php echo $drm->transmission_douane->id_declaration ?>.
<?php elseif ($drm->transmission_douane->xml): ?><p style="text-align:left;">
La transmission a échoué. Le message d'erreur envoyé par le portail des douanes est : <?php echo str_replace('\n', '<br/> - ', $drm->getTransmissionErreur()); ?></p>
<?php endif ; elseif ($drm->exist("_attachments") && $drm->_attachments->exist("drm_transmise.xml")): ?>
Cette DRM semble avoir ét étransmise et réouverte.
<?php else: ?>
Cette DRM n'a pas été transmise.
<?php endif; ?>
<?php if (!$isTeledeclarationMode && $drm->isValidee()): ?>
        &nbsp;<a id="retransmission" data-link="<?php echo url_for('drm_retransmission', $drm); ?>" class="btn_majeur"  style="font-weight: normal; line-height: 20px; float:right;" ><span style="font-size:7pt;">retransmettre</span></a>
<?php endif; ?>
            </td></tr>
            <?php if (!$isTeledeclarationMode || $sf_user->isUsurpationCompte()):
                if (!$drm->exist('transmission_douane') || ($drm->exist('transmission_douane') && is_null($drm->transmission_douane->coherente))) : ?>
                <tr><td>Aucun retour de la part de proDou@ne n'a été effectué&nbsp;
                    <a href="<?php echo url_for('drm_retour_refresh', $drm); ?>"  class="btn_majeur"  style="line-height: 20px; font-size:25px; float:right;" >♲</a>
                </td></tr>
                <?php elseif($drm->exist('transmission_douane') && $drm->transmission_douane->coherente): ?>
                <tr>
                    <td>La DRM est <strong>conforme</strong> à celle de proDou@ne
                        <a href="<?php echo url_for('drm_retour_refresh', $drm); ?>"  class="btn_majeur"  style="line-height: 20px; font-size:25px; float:right;" >♲</a>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td>La DRM n'est <strong>pas conforme</strong> à celle de proDou@ne
                        <a href="<?php echo url_for('drm_retour_refresh', $drm); ?>"  class="btn_majeur"  style="line-height: 20px; font-size:25px; float:right;" >♲</a>

                    </td>
                </tr>

              <?php endif; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php

    if (((!$isTeledeclarationMode) || $sf_user->isUsurpationCompte()) && $drm->exist('transmission_douane') && $drm->exist('_attachments') && $drm->_attachments->exist('drm_retour.xml') && (!$drm->transmission_douane->coherente)): ?>
      <br/>
      <table class="table_recap">
        <thead >
            <tr>
                <th colspan="3"><label style="float: left; padding : 0 10px;">Identification du problème rencontré (<a href="<?php echo url_for('drm_xml_table', array('identifiant' => $drm->identifiant,"periode_version" => $drm->getPeriodeAndVersion(), 'retour' => "1")); ?>">XML reçu CIEL</a> - <a href="<?php echo url_for('drm_edition_libelles', array('identifiant' => $drm->identifiant,"periode_version" => $drm->getPeriodeAndVersion())); ?>">Edition libellés</a>)</label></th>
                <th>Valeur proDou@ne</th>
                <th>Valeur Interpro</th>
            </tr>
        </thead>
      <tbody>
      <?php foreach ($drm->getXMLComparison()->getFormattedXMLComparaison() as $problemeSrc => $values): ?>
          <?php if(preg_match('/^(\[.+\]) (.+)((entree|sortie|stock).+)$/', $problemeSrc, $matches)): ?>
        <tr>
          <td style="text-align: left; "><?php echo $matches[1]; ?></td>
          <td style="text-align: left;"><?php echo $matches[2]; ?></td>
          <td style="text-align: left;"><?php echo $matches[3]; ?></td>
          <td><?php echo $values[0]; ?></td>
          <td><?php echo $values[1]; ?></td>
        </tr>
        <?php else: ?>
            <tr>
                <td colspan="3" ><?php echo $problemeSrc; ?></td>
                <td><?php echo $values[0]; ?></td>
                <td><?php echo $values[1]; ?></td>
            </tr>
        <?php endif; ?>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
</div>
<br/>
