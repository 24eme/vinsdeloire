<?php include_partial('global/navTop', array('active' => 'drm')); ?>
<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'declaratif', 'pourcentage' => '70')); ?>

    <section id="principal">
        <div id="application_dr">
            <form id="declaratif_info" action="<?php echo url_for('drm_declaratif', $drm) ?>" method="post">
                <?php echo $form->renderHiddenFields() ?>
                
                <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
                <?php if ($form->getObject()->isRectificative()): ?>
                <ul class="onglets_declaratif">
                    <li><strong>Administrateur</strong></li>
                </ul>

                <div class="contenu_onglet_declaratif ">
                    <p class="intro"><?php echo $form['raison_rectificative']->renderLabel() ?><a href="" class="msg_aide" data-msg="help_popup_declaratif_raison_rectificative" title="Message aide"></a></p>
                    <div class="ligne_form alignes">
                        <?php echo $form['raison_rectificative']->renderError() ?>
                        <?php echo $form['raison_rectificative']->render() ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($form->getObject()->isValidee()): ?>
                <ul class="onglets_declaratif">
                    <li><strong>Date signature</strong><a href="" class="msg_aide" data-msg="help_popup_declaratif_date_signature" title="Message aide"></a></li>
                </ul>

                <div class="contenu_onglet_declaratif ">
                    <p class="intro"><?php echo $form['date_signee']->renderLabel() ?><a href="" class="msg_aide" data-msg="help_popup_declaratif_date_signee" title="Message aide"></a></p>
                    <div class="ligne_form alignes">
                        <?php echo $form['date_signee']->renderError() ?>
                        <?php echo $form['date_signee']->render() ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <ul class="onglets_declaratif">
                    <li><strong>Défaut d'apurement</strong><a href="" class="msg_aide" data-msg="help_popup_declaratif_defaut_apurement" title="Message aide"></a></li>
                </ul>

                <div class="contenu_onglet_declaratif ">
                    <p class="intro">Veuillez séléctionner un défaut d'apurement :</p>
                    <div class="ligne_form alignes">

                        <?php echo $form['apurement']->renderError() ?>
                        <?php echo $form['apurement']->render() ?>

                    </div>
                </div>
                
		<?php if ($form->getObject()->hasVrac()): ?>
                <ul class="onglets_declaratif">
                    <li><strong>Mouvements au cours du mois</strong><a href="" class="msg_aide" data-msg="help_popup_declaratif_mouvement" title="Message aide"></a></li>
                </ul>

                <div class="contenu_onglet_declaratif">
                    <p class="intro">Documents prévalidés ou N° empreinte utilisés au cours du mois</p>

                    <div class="champs_centres">
                        <h3>DAA</h3>

                        <div class="ligne_form">
                            <?php echo $form['daa_debut']->renderLabel() ?><?php echo $form['daa_debut']->render() ?>
                            <?php echo $form['daa_fin']->renderLabel() ?><?php echo $form['daa_fin']->render() ?>
                        </div>
                        <div class="ligne_form alignes">
                            <?php echo $form['daa_debut']->renderError() ?>
                            <?php echo $form['daa_fin']->renderError() ?>
                        </div>

                        <h3>DSA</h3>

                        <div class="ligne_form">
                            <?php echo $form['dsa_debut']->renderLabel() ?><?php echo $form['dsa_debut']->render() ?>
                            <?php echo $form['dsa_fin']->renderLabel() ?><?php echo $form['dsa_fin']->render() ?>
                        </div>
                        <div class="ligne_form alignes">
                            <?php echo $form['dsa_debut']->renderError() ?>
                            <?php echo $form['dsa_fin']->renderError() ?>
                        </div>
                    </div>
                    <div class="ligne_form ligne_entiere ecart_check">
                        <?php echo $form['adhesion_emcs_gamma']->render() ?><?php echo $form['adhesion_emcs_gamma']->renderLabel() ?><?php echo $form['adhesion_emcs_gamma']->renderError() ?>
                    </div>
                </div>
                <?php endif; ?>

                <ul class="onglets_declaratif">
                    <li><strong>Caution</strong><a href="" class="msg_aide" data-msg="help_popup_declaratif_caution" title="Message aide"></a></li>
                </ul>

                <div class="contenu_onglet_declaratif">
                    <p class="intro">Veuillez indiquer si vous disposez d'une caution, si oui merci de préciser l'organisme :</p>
                    <div class="ligne_form alignes" id="caution_accepte">
                        <?php echo $form['caution']->renderError() ?>
                        <?php echo $form['caution']->render() ?>
                    </div>

                    <div class="ligne_form alignes" id="organisme" style="display:<?php echo ($form['caution']->getValue() === 0) ? 'block' : 'none' ?>;">
                        <?php echo $form['organisme']->renderError() ?>
                        <?php echo $form['organisme']->renderLabel() ?>
                        <?php echo $form['organisme']->render() ?>
                    </div>
                </div>

                <ul class="onglets_declaratif">
                    <li><strong>Paiement des droits de circulation</strong><a href="" class="msg_aide" data-msg="help_popup_declaratif_paiement" title="Message aide"></a></li>
                </ul>
                <div class="contenu_onglet_declaratif">
                    
                    <?php if ($hasFrequencePaiement): ?>
                        <?php if (DRMPaiement::isDebutCampagne()): ?>
                            <p class="intro"><?php echo $form['frequence']->renderLabel() ?></p>
                            <div class="ligne_form alignes">
                                <?php echo $form['frequence']->renderError() ?>
                                <?php echo $form['frequence']->render() ?>
                            </div>
                        <?php else: ?>
                            <div class="ligne_form alignes">
                                Vous payez par échéance <strong><?php echo strtolower($drm->declaratif->paiement->douane->frequence) ?></strong>
                                - <a href="<?php echo url_for('drm_declaratif_frequence_form', $drm) ?>" class="btn_popup" data-popup="#popup_ajout_frequence" data-popup-config="configForm">Modifier l'échéance de paiement</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="intro"><?php echo $form['frequence']->renderLabel() ?></p>
                        <div class="ligne_form alignes">
                            <?php echo $form['frequence']->renderError() ?>
                            <?php echo $form['frequence']->render() ?>
                        </div>
                    <?php endif; ?>
                    <br />
                    <p class="intro"><?php echo $form['moyen_paiement']->renderLabel() ?><p>
                    <div class="ligne_form alignes">
                        <?php echo $form['moyen_paiement']->renderError() ?>
                        <?php echo $form['moyen_paiement']->render() ?>
                    </div>
                </div>

                <div id="btn_etape_dr">
                	<?php if (!$drm->declaration->hasMouvementCheck()): ?>
                	<a href="<?php echo url_for('drm_mouvements_generaux', $drm) ?>" class="btn_prec"><span>Précédent</span></a>
                	<?php else: ?>
                    <a href="<?php echo url_for('drm_vrac', array('sf_subject' => $drm, 'precedent' => '1')) ?>" class="btn_prec"><span>Précédent</span></a>
                    <?php endif; ?>
                    <button type="submit" class="btn_suiv"><span>suivant</span></button>
                </div>
            </form>
        </div>
    </section>
</section>

<script language="javascript">

$(document).ready( function()
	{
        $('#drm_declaratif_caution_0').click(function() { $('#organisme').css('display', 'block') });
        $('#drm_declaratif_caution_1').click(function() { $('#organisme').css('display', 'none') });
    });
</script>