<li>
    <p class="mois"><?php echo $calendrier->getPeriodeLibelle($periode) ?></p>
    <?php if ($calendrier->getStatut($periode) == DRMCalendrier::STATUT_VALIDEE): ?>
        <ul class="mois_infos valide_campagne">
            <li><label for="">&Eacute;tat:</label><span>Validée</span></li>
            <li><label for="">N&deg;&nbsp;:</label><?php echo $calendrier->getNumeroArchivage($periode) ?></li>
            <li><a class="action" href="<?php echo url_for('drm_visualisation', array('identifiant' => $calendrier->getIdentifiant(), 'periode_version' => $calendrier->getPeriodeVersion($periode))) ?>">Voir la drm</a></li>
        </ul>
    <?php elseif ($calendrier->getStatut($periode) == DRMCalendrier::STATUT_EN_COURS): ?>
        <ul class="mois_infos attente_campagne">
            <li><label for="">&Eacute;tat:</label><span>En attente</span></li>
            <li><label for="">&nbsp;</label><span><a href="<?php echo url_for('drm_delete', array('identifiant' => $calendrier->getIdentifiant(), 'periode_version' => $calendrier->getPeriodeVersion($periode))); ?>"><b>Supprimer</b></a></li></span></li>
            <li><a class="action" href="<?php echo url_for('drm_init', array('identifiant' => $calendrier->getIdentifiant(), 'periode_version' => $calendrier->getPeriodeVersion($periode))); ?>">Términer la saisie</a>
        </ul>
    <?php elseif ($calendrier->getStatut($periode) == DRMCalendrier::STATUT_NOUVELLE): ?>
        <ul class="mois_infos nouv_campagne">
            <li><label for="">&Eacute;tat:</label><span>Nouvelle</span></li>
            <li><label for="">&nbsp;</label></li>
            <li><a class="action" href="<?php echo url_for('drm_nouvelle', array('identifiant' => $calendrier->getIdentifiant(), 'periode' => $periode)) ?>">Créer une drm</a></li>
        </ul>
    <?php endif; ?>
</li>