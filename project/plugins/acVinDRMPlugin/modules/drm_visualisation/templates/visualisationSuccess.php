<?php use_helper("Date"); ?>
<?php use_helper('DRM'); ?>
<!-- #principal -->
<section id="principal" class="drm">
    <?php if (!$isTeledeclarationMode): ?>
        <?php include_partial('drm/header', array('drm' => $drm)); ?>
        <ul id="recap_infos_header">
            <li>
                <label>Nom de l'opérateur : </label><?php echo $drm->getEtablissement()->nom ?>
            </li>
            <li>
                <strong>
                  <label><?php if($drm->isTeledeclare()): ?>Télédéclarée<?php if($drm->hasBeenTransferedToCiel()): ?>&nbsp;transmise<?php endif; ?><?php if($drm->exist('transmission_douane') && $drm->transmission_douane->coherente): ?>&nbsp;- Douane OK<?php endif; ?><?php if($drm->isFactures()): ?>&nbsp;(facturée)<?php endif; ?><?php if($drm->isNonFactures()): ?>&nbsp;(non facturée)<?php endif; ?>
                  <?php else : ?>Saisie sur Vinsi<?php endif; ?>
                </label>
                    <?php if (!$isTeledeclarationMode && !$drm->isTeledeclare()): ?>
                        <label style="margin-left: 150px;"><?php echo 'Numéro d\'archive : ' . $drm->numero_archive; ?></label>
                    <?php endif; ?>
                    <label style="float: right;">Période : <?php echo $drm->periode ?></label></strong>
            </li>
        </ul>
    <?php else: ?>
        <h2><?php echo getDrmTitle($drm); ?> <small style="font-weight: normal; text-transform: none;">(Validée le <?php echo format_date($drm->valide->date_signee, "dd/MM/yyyy", "fr_FR"); ?>)</small> &nbsp;<a href="" class="msg_aide_drm  icon-msgaide" title="<?php echo getHelpMsgText('drm_visualisation_aide1'); ?>"></a></h2>
        <?php if ($drm->isNegoce()): ?>
          <fieldset id="espace_prelevement">
            <div id="mon_espace">
              <div class="espace_prelevement">
                <div class="panel">
                  <ul style="height: auto" class="societe_prelevement">
                    <li style="height: auto">
                      <div class="adhesion_prelevement">
                        <img src="/images/visuels/prodouane.png" />
                        <p><br />Vous pouvez à présent télécharger votre DRM au format XML afin de l'importer en DTI+ sur le site de la douane via le lien suivant : <a href="https://douane.gouv.fr/">douane.gouv.fr</a><br />
                        <br />
                        <a class="btn_majeur" style="float:right;" download="<?= $drm->_id ?>.xml" target="_blank" href="<?php echo url_for('drm_xml', $drm); ?>">Télécharger le XML</a><br />&nbsp;</p>
                      </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </fieldset>
        <?php endif; ?>

        <?php if ($drm->isTeledeclare()): ?>
            <div id="btn_etape_dr" style="text-align: center;">
                <a href="<?php echo url_for('drm_pdf', $drm); ?>" class="btn_majeur btn_pdf center" id="drm_pdf"><span>Télécharger le PDF</span></a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div id="drm_validation_coordonnees">
        <div class="drm_validation_societe">
            <?php include_partial('drm_visualisation/societe_infos', array('drm' => $drm, 'isModifiable' => false)); ?>
        </div>
        <div class="drm_validation_etablissement">
            <?php include_partial('drm_visualisation/etablissement_infos', array('drm' => $drm, 'isModifiable' => false)); ?>
        </div>
    </div>
    <?php if (!$isTeledeclarationMode): ?>
        <?php if ($drm_suivante && $drm_suivante->isRectificative() && !$drm_suivante->isValidee()):
            ?>
            <div class="vigilance_list">
                <ul>
                    <li><?php echo MessagesClient::getInstance()->getMessage('msg_rectificatif_suivante') ?></li>
                </ul>
            </div>
        <?php endif; ?>
        <div style="text-align: right;">
        <?php $avertissementTransfere = "onclick=\"return confirm('Attention cette DRM a été déclarée aux douanes et est conforme.\nLa modifier peut avoir des impacts pour les prochains mois si le stock change.\n\nVeux-tu vraiment le faire ?');\"";?>
        <?php if ($drm->isReouvrable()): ?>
            <a class="btn_majeur btn_modifier" <?php if($drm->hasBeenTransferedToCiel()):?><?php echo $avertissementTransfere; ?><?php endif; ?> href="<?php echo url_for('drm_reouvrir', $drm) ?>">Ré-ouvrir la DRM</a>
        <?php elseif ($drm->isModifiable()): ?>
                <a class="btn_majeur btn_modifier" <?php if($drm->isTeledeclare() && $drm->hasBeenTransferedToCiel()):?><?php echo $avertissementTransfere; ?><?php endif; ?> href="<?php echo url_for('drm_modificative', $drm) ?>">Modificatrice de la DRM</a>
        <?php endif; ?>
        </div>

        <?php if (!$drm->isMaster()): ?>
            <fieldset id="points_vigilance">
                <ul>
                    <li class="warning">Ce n'est pas la <a href="<?php echo ($drm->getMaster()->isValidee())? url_for('drm_visualisation', $drm->getMaster()) :  url_for('drm_redirect_etape', $drm->getMaster()) ?>">dernière version</a> de la DRM, le tableau récapitulatif n'est donc pas à jour.</li>
                </ul>
            </fieldset>
        <?php endif; ?>
    <?php endif; ?>
    <?php include_partial('drm_visualisation/recap_stocks_mouvements', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode, 'no_link' => $no_link, 'mouvementsByProduit' => $mouvementsByProduit, 'visualisation' => true, 'typeDetailKey' => DRM::DETAILS_KEY_SUSPENDU, 'typeKey' => DRMClient::TYPE_DRM_SUSPENDU)) ?>
    <?php include_partial('drm_visualisation/recap_stocks_mouvements', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode, 'no_link' => $no_link, 'mouvementsByProduit' => $mouvementsByProduit, 'visualisation' => true, 'typeDetailKey' => DRM::DETAILS_KEY_ACQUITTE, 'typeKey' => DRMClient::TYPE_DRM_ACQUITTE)) ?>

    <?php include_partial('drm_visualisation/reserveinterpro', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode )); ?>

    <?php if ($isTeledeclarationMode): ?>
        <?php include_partial('drm_visualisation/recap_crds', array('drm' => $drm)) ?>
        <?php include_partial('drm_visualisation/recapAnnexes', array('drm' => $drm)) ?>
    <?php endif; ?>
    <?php include_partial('drm_visualisation/recapDroits', array('drm' => $drm, 'recapCvo' => $recapCvo, 'isTeledeclarationMode' => $isTeledeclarationMode)) ?>
    <br />
    <?php if ($drm->exist('transmission_douane')) : ?>
    <?php include_partial('drm_visualisation/transmission_douane', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode)); ?>
    <?php endif; ?>

    <?php if (!$isTeledeclarationMode): ?>
        <br/>
        <table class="table_recap">
            <tr><th>Commentaire</th></tr>
            <tr><td><pre class="commentaire"><?php echo $drm->commentaire; ?></pre></td></tr>
        </table>
    <?php endif; ?>

    <div id="btn_etape_dr">
        <a href="<?php echo url_for('drm_etablissement', array('identifiant' => $drm->identifiant)); ?>" class="btn_etape_prec"><span>Retour à mon espace</span></a>
        <?php if ($isTeledeclarationMode) : ?>
            <a style="margin-left: 70px;" href="<?php echo url_for('drm_pdf', $drm); ?>" class="btn_majeur btn_pdf center" id="drm_pdf"><span>Télécharger le PDF</span></a>
            <?php if($compte->hasDroit("teledeclaration_douane") && $isTeledeclarationMode && !$drm->isNegoce()): ?>
              <?php if (!$drm->exist('transmission_douane') || !$drm->transmission_douane->success) : ?>
                <a style="margin-left: 5px;" href="<?php echo url_for('drm_transmission', $drm); ?>" class="btn_majeur btn_vert" ><span>Transmettre la Drm sur CIEL</span></a>
              <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php
include_partial('drm/colonne_droite', array('drm' => $drm, 'isTeledeclarationMode' => $isTeledeclarationMode));
?>
