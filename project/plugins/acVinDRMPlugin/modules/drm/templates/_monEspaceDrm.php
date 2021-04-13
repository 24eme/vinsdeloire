<?php use_helper('Date'); ?>
<?php use_helper('DRM'); ?>
<?php use_helper('Orthographe'); ?>
<fieldset id="espace_drm">
    <div id="mon_espace">
        <div class="<?php echo (!$accueil_drm) ? 'block_teledeclaration' : ''; ?>  espace_drm">
            <?php if (!$accueil_drm): ?>
                <div class="title <?php echo ($accueil_drm) ? 'title_espace' : ''; ?>">ESPACE DRM</div>
            <?php endif; ?>
            <div class="panel">
                <ul style="<?php if (!isset($btnAccess)): ?>height: auto<?php endif; ?>" class="etablissements_drms">
                    <?php
                    //$haslegalpopup = false;
                    foreach ($lastDrmToCompleteAndToStart as $etb => $drmsByEtb) :
                        ?>
                        <li>
                            <div class="etablissement_drms">
                                <h2> <?php echo $drmsByEtb->nom . ' (' . $etb . ')'; ?></h2>
                                <ul class="block_drm_espace">
                                    <?php if ($drmsByEtb->statut == DRMCalendrier::STATUT_EN_COURS): ?>
                                        <li class="statut_toFinish">
                                            <a href="<?php echo url_for('drm_redirect_etape', $drmsByEtb->drm); ?>" ><span>Finir la DRM <?php echo ($drmsByEtb->periode)? getFrPeriodeElision($drmsByEtb->periode) : ""; ?></span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($drmsByEtb->statut == DRMCalendrier::STATUT_EN_COURS_NON_TELEDECLARE): ?>
                                        <li class="statut_toFinish_non_teledeclare">
                                            <span>La DRM <?php echo getFrPeriodeElision($drmsByEtb->periode); ?> est en cours de saisie à Interloire</span>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ($drmsByEtb->statut == DRMCalendrier::STATUT_NOUVELLE): ?>
                                        <?php
                                        $lienNouvelle = url_for('drm_nouvelle', array('identifiant' => $etb, 'periode' => $drmsByEtb->periode));
                                        if ($isTeledeclarationMode) {
                                            $lienNouvelle = url_for('drm_etablissement', array('identifiant' => $etb)) . '#drm_nouvelle_' . $drmsByEtb->periode . '_' . $etb;
                                            if (!$hasNoPopupCreation && $etablissement->hasLegalSignature()) {
                                                //gestion de la popup de création
                                                include_partial('drm/creationDrmPopup', array('periode' => $drmsByEtb->periode, 'identifiant' => $etb, 'drmCreationForm' => $drmsToCreateForms[$etb . '_' . $drmsByEtb->periode]));
                                            }
                                        }
                                        if ($isTeledeclarationMode && !$etablissement->hasLegalSignature()) {
                                            //  if (!$haslegalpopup)
                                            echo '<li class="statut_toCreate"><a href="' . url_for('drm_societe', array('identifiant' => $etablissement->getIdentifiant())) . '"><span>Activer votre espace DRM</span></a></li>';
                                            //$haslegalpopup = true;
                                        } else {
                                            echo '<li class="statut_toCreate"><a href="' . $lienNouvelle . '" class="';
                                            echo ($isTeledeclarationMode) ? 'drm_nouvelle_teledeclaration' : '';
                                            echo '"><span>Créer la DRM ' . getFrPeriodeElision($drmsByEtb->periode) . '</span></a></li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                    <?php if ($drmsByEtb->statut == DRMCalendrier::STATUT_VALIDEE): ?>
                                        <?php if ($etablissement->hasLegalSignature()) { ?>
                                            <li class="statut_validee">
                                            <?php if ($drmsByEtb->drm && $drmsByEtb->periode): ?>
                                                <a href="<?php echo url_for('drm_visualisation', $drmsByEtb->drm); ?>">
                                                    <span>Visualiser votre DRM <?php echo getFrPeriodeElision($drmsByEtb->periode); ?></span>
                                                </a>
                                            <?php endif; ?>
                                            </li>
                                            <?php
                                        } else {
                                            echo '<li class="statut_toCreate"><a href="' . url_for('drm_societe', array('identifiant' => $etablissement->getIdentifiant())) . '"><span>Activer votre espace DRM</span></a></li>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (isset($btnAccess)): ?>
                    <div class="acces">
                        <a href="<?php echo url_for('drm_societe', array('identifiant' => $etablissement->getIdentifiant())) ?>" class="btn_majeur">Accéder aux DRM</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</fieldset>
