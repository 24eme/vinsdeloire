<?php use_helper('Float'); ?>
<?php use_helper('Rectificative'); ?>
<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
	<?php if (!$hide_rectificative): ?>
    <form method="GET" action="<?php echo url_for(array('sf_route' => 'drm_rectificative', 'identifiant' => $sf_user->getTiers()->identifiant, 'periode_version' => $drm->getPeriodeAndVersion())) ?>">
        <button class="btn_passer_etape rectificative" type="submit">Soumettre une DRM rectificative</button>
    </form>
	<?php endif; ?>
    <!-- #principal -->
    <section id="principal">

        <?php if ($drm_suivante && $drm_suivante->isRectificative() && !$drm_suivante->isValidee()): ?>
            <div class="vigilance_list">
                <ul>
                    <li><?php echo MessagesClient::getInstance()->getMessage('msg_rectificatif_suivante') ?></li>
                </ul>
            </div>
        <?php endif; ?>
        <div id="contenu_onglet">

            <?php if ($drm->declaration->hasMouvement() && !$drm->declaration->hasStockEpuise()): ?>
                <?php include_partial('drm/recap', array('drm' => $drm)) ?>
                <?php include_partial('drm/droits', array('drm' => $drm)) ?>
            <?php else: ?>
                <?php include_partial('drm/pasDeMouvement', array('drm' => $drm)) ?>
            <?php endif; ?>

            
            <a id="telecharger_pdf" href="<?php echo url_for('drm_pdf', array('identifiant' => $sf_user->getTiers()->identifiant, 'periode_version' => $drm->getPeriodeAndVersion())) ?>">Télécharger le PDF</a>
                
            <div id="btn_etape_dr">
                <?php if ($drm_suivante && $drm_suivante->isRectificative()): ?>
                    <a href="<?php echo url_for('drm_init', array('identifiant' => $sf_user->getTiers()->identifiant, 'periode_version' => $drm_suivante->getPeriodeAndVersion())) ?>" class="btn_suiv">
                        <span>Passer à la DRM suivante</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo url_for('drm_etablissement') ?>" class="btn_suiv">
                        <span>Retour à mon espace</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>    
    </section>
</section>