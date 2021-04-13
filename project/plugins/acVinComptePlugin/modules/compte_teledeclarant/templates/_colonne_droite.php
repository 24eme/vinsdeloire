<?php
use_helper("Date");
$btn = false;
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

                <?php if (!$btn && $sf_user->isUsurpationCompte() && $sf_user->hasTeledeclarationVrac()): ?>
                  <?php $btn = true; ?>
                    <div class="ligne_btn txt_centre">
                        <a class="deconnexion btn_majeur btn_orange" href="<?php echo url_for('vrac_dedebrayage') ?>">Revenir sur VINSI</a>
                    </div>
                <?php endif; ?>
                <?php if (!$btn && $sf_user->isUsurpationCompte() && $sf_user->hasTeledeclarationDrm()): ?>
                  <?php $btn = true; ?>
                    <div class="ligne_btn txt_centre">
                        <a class="deconnexion btn_majeur btn_orange" href="<?php echo url_for('drm_dedebrayage') ?>">Revenir sur VINSI</a>
                    </div>
                <?php endif; ?>
                <?php if (!$btn && $sf_user->isUsurpationCompte() && $sf_user->hasTeledeclarationFacture()): ?>
                  <?php $btn = true; ?>
                    <div class="ligne_btn txt_centre">
                        <a class="deconnexion btn_majeur btn_orange" href="<?php echo url_for('facture_dedebrayage') ?>">Revenir sur VINSI</a>
                    </div>
                <?php endif; ?>

                <div class="ligne_btn txt_centre">
                    <?php if (isset($retour) && $retour): ?>
                        <a href="<?php echo url_for('vrac_societe', array('identifiant' => str_replace('COMPTE-', '', $societe->compte_societe))); ?>" class="btn_majeur btn_acces">Mes Contrats</a>
                    <?php endif; ?>
                </div>
                <div class="ligne_btn txt_centre">
                    <?php if (isset($etablissementPrincipal) && ($etablissementPrincipal->isCourtier() || $etablissementPrincipal->isNegociant())): ?>
                        <a href="<?php echo url_for('annuaire', array('identifiant' => $etablissementPrincipal->identifiant)); ?>" class="btn_majeur btn_annuaire">Annuaire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    end_slot();
endif;
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
            <br/>
            <br/>
            <p class="lien_lecteur_pdf">
                Ce document est au format PDF. Pour la visualiser, veuillez utiliser un <a target="_blank" href="<?php echo sfConfig::get('app_pdf_reader_link') ?>">lecteur PDF</a>.
            </p>

        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function ()
        {
            initNoticePopup();
        });
    </script>
    <?php
    end_slot();
    ?>
<?php endif; ?>
