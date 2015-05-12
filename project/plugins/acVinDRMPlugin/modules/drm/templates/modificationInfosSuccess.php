<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'informations', 'pourcentage' => '5')); ?>

    <!-- #principal -->
    <section id="principal">
        <div id="application_dr">
            <div id="drm_informations">
                <p><?php echo acCouchdbManager::getClient('Messages')->getMessage('msg_modification_infos'); ?></p>
                <br /><br />
                <a href="<?php echo url_for('drm_etablissement') ?>" class="btn_suiv">
                    <span>Retour à mon espace</span>
                </a>
            </div>
        </div>
    </section>
</section>
