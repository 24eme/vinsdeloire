<?php include_partial('global/navTop', array('active' => 'drm')); ?>

<section id="contenu">

    <?php include_partial('drm/header', array('drm' => $drm)); ?>
    <?php include_component('drm', 'etapes', array('drm' => $drm, 'etape' => 'informations', 'pourcentage' => '5')); ?>

    <!-- #principal -->
    <section id="principal">
		<div id="application_dr">
			<div id="drm_informations">
		        <p>Veuillez tout d'abord confirmer les informations ci-dessous :<br /><br /></p>
		        <form action="<?php echo url_for('drm_informations', $drm) ?>" method="post">
			        <?php echo $form->renderGlobalErrors() ?>
					<?php echo $form->renderHiddenFields() ?>
                                        <div class="ligne_form">
						<?php echo $form['confirmation']->renderError() ?>
					</div>
					<div class="ligne_form">
						<label for="champ_4">Raison sociale :</label>
						<span class="valeur valeur_2"><?php echo $tiers->raison_sociale ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_4">Nom commercial :</label>
						<span class="valeur valeur_2"><?php echo $tiers->nom ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_1">CVI :</label>
						<span class="valeur"><?php echo $tiers->cvi ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_2">N° SIRET :</label>
						<span class="valeur"><?php echo $tiers->siret ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_3">N° entrepositaire agréé :</label>
						<span class="valeur"><?php echo $tiers->no_tva_intracommunautaire ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_6">Adresse du chai :</label>
						<span class="valeur"><?php echo $tiers->siege->adresse ?><br /><?php echo $tiers->siege->code_postal ?> <?php echo $tiers->siege->commune ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_7">Lieu ou est tenue la comptabilité matière :</label>
						<span class="valeur"><?php if (!$tiers->comptabilite->adresse): ?>IDEM<?php else: ?><?php echo $tiers->comptabilite->adresse ?><br /><?php echo $tiers->comptabilite->code_postal ?> <?php echo $tiers->comptabilite->commune ?><?php endif; ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_8">Service des douanes :</label>
						<span class="valeur"><?php echo $tiers->service_douane ?></span>
					</div>
					<div class="ligne_form">
						<label for="champ_9">Numéro d’Accise :</label>
						<span class="valeur">1654546764</span>
					</div>
					<?php if(!$sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
					<?php echo $form['confirmation']->render() ?>
					<?php endif; ?>
					<div class="ligne_btn">
						<button type="submit" class="btn_suiv"><span>VALIDER</span></button>
						<a href="#" class="btn_popup btn_popup_trigger" data-popup="#popup_confirm_modif_infos" data-popup-config="configConfirmModifInfos" data-popup-titre="Etes-vous sûr de vouloir modifier ces informations ?"></a>
					</div>
		        </form>
			</div>
	    </div>
    </section>
</section>
