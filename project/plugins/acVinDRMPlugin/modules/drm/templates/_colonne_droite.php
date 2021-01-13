<?php
use_helper("Date");
use_helper('DRM');
use_helper('Orthographe');
if (!isset($isMonEspace)) {
    $societe = $drm->getEtablissement()->getSociete();
    $etablissementPrincipal = $societe->getEtablissementPrincipal();
}
?>
<?php
if ($isTeledeclarationMode):
    slot('colCompte');
    ?>
    <div class="bloc_col" id="contrat_compte">
        <h2><?php echo $etablissementPrincipal->famille; ?> (<?php echo $societe->identifiant; ?>) </h2>

        <div class="contenu">
            <div class="text-center" style="text-align: center;">
                <p><strong><?php echo $societe->raison_sociale; ?></strong></p>

                <p> (<?php echo $societe->siege->commune; ?>) </p>

                <?php if ($sf_user->isUsurpationCompte()): ?>
                    <div class="ligne_btn txt_centre">
                        <a class="deconnexion btn_majeur btn_orange" href="<?php echo url_for('drm_dedebrayage') ?>">Revenir sur VINSI</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="bloc_col" id="contrat_compte">
        <h2>Mémo</h2>
        <?php if(isset($drm) && $drm && $memo = DRMClient::getInstance()->getMemoForMonth($drm)): ?>
        <div class="contenu fadediv" style="opacity:0;">
          <p><span class="picto"></span><strong class="memo-danger">Mémo du mois <?php echo elision('de', format_date(preg_replace("/^([0-9]{4})([0-9]{2})/","$1-$2-01",$drm->periode), "MMMM", "fr_FR")); ?></strong> :</p>
          <br/>
          <p class=""><?php echo $memo; ?></p>
        </div>
      <?php endif; ?>
        <div class="contenu">
          <p>Vos <strong>questions fréquentes : </strong></p>
          <a href="/data/Vos questions fréquentes - DRM.pdf" id="lien_faq" class="lien_telechargement">Télécharger les questions</a>

          <p class="lien_lecteur_pdf">
              Ces documents sont au format PDF. Pour les visualiser, veuillez utiliser un <a target="_blank" href="<?php echo sfConfig::get('app_pdf_reader_link') ?>">lecteur PDF</a>.
          </p>
        </div>
        <br/>
    </div>
    <?php
    end_slot();
endif;
?>

<!-- COLONNE ACTION -->
<?php
slot('colButtons');
$url_retour_espace = null;
$text_retour_espace = 'Actions';
if (isset($drm)) {
    $url_retour_espace = ($isTeledeclarationMode && !$sf_user->hasCredentialAdmin()) ?
            url_for('drm_societe', array('identifiant' => $etablissementPrincipal->identifiant)) : url_for('drm_etablissement', array('identifiant' => $drm->identifiant));
    $text_retour_espace = ($isTeledeclarationMode && !$sf_user->hasCredentialAdmin()) ? 'Retour à mes DRM' : "Retour au calendrier";
}
?>
<div class="bloc_col" >
    <h2><?php echo ($isTeledeclarationMode) ? 'Actions' : $text_retour_espace ?></h2>
    <div class="contenu">
        <?php if ($isTeledeclarationMode): ?>
            <div class="ligne_btn txt_centre">
                <span style="font-weight: bold;"><?php echo (isset($isMonEspace)) ? 'Mon Espace DRM' : getDrmTitle($drm); ?></span>
            </div>
        <?php endif; ?>
        <div class="ligne_btn txt_centre">
            <?php if (isset($drm) || $isTeledeclarationMode): ?>
                <?php if (!isset($isMonEspace)): ?>
                    <a href="<?php echo $url_retour_espace; ?>" class="btn_majeur btn_acces"><?php echo $text_retour_espace; ?> </a>
                    <?php if (!$drm->isValidee()): ?>
                        <a class="drm_delete_lien_colonne lien_drm_supprimer">
                            <span>Supprimer la DRM</span>
                        </a>
                    <?php endif; ?>
                <?php elseif ($isTeledeclarationMode): ?>
                    <a href="<?php echo url_for('compte_teledeclarant_mon_espace', array('identifiant' => $etablissementPrincipal->identifiant)); ?>" class="btn_majeur btn_acces">Retour à mon espace</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php if (!$isTeledeclarationMode): ?>
            <div class="ligne_btn txt_centre">
                <div class="btnConnexion">
                    <a href="<?php echo url_for('drm_debrayage', array('identifiant' => $etablissementPrincipal->identifiant)); ?>" class="btn_majeur lien_connexion"><span>Connexion à la télédecl.</span></a>
                </div>
            </div>
            <div class="text-center" style="text-align: center;">
                <p><strong><?php echo $etablissementPrincipal->nom; ?></strong></p>
<?php if (!$etablissementPrincipal->isActif()) :?>
                <p><strong style="color: red;">SUSPENDU</strong></p>

<?php endif; ?>
                <p><strong><?php echo $etablissementPrincipal->identifiant; ?></strong></p>
                <p> (<?php echo $etablissementPrincipal->getMasterCompte()->commune; ?>) </p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php end_slot();
?>

<?php if ($isTeledeclarationMode): ?>
    <!-- COLONNE D'AIDE -->
    <?php
    slot('colAide');
    $contact = EtablissementClient::getInstance()->buildInfosContact($etablissementPrincipal);
    ?>
    <div class="bloc_col" id="contrat_aide">
        <h2>Aide</h2>

        <div class="contenu">
            <p>
                En cas de besoin n'hésitez pas à consulter le <strong>guide utilisateur</strong> au format pdf.
            </p>
            <a href="/data/Enregistrez_vos_DRM_en_ligne-Guide_utilisateur_Janvier_2021.pdf" id="liens_notices" class="lien_telechargement">Télécharger le guide</a>

            <h3>Votre contact</h3>

            <ul class="contact">
                <li class="nom"><?php echo $contact->nom; ?></li>
                <li class="email"><a href="mailto:<?php echo $contact->email; ?>"><?php echo $contact->email; ?></a></li>
                <li class="telephone"><?php echo $contact->telephone; ?></li>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function ()
        {
            initNoticePopup();
            initMemoAnimation();
        });
    </script>
    <?php
    end_slot();
    ?>
<?php endif; ?>
