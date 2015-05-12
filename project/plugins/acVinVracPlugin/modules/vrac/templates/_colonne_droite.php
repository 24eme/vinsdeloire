<?php
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
                <a class="deconnexion btn_majeur btn_orange" href="<?php echo url_for('vrac_dedebrayage') ?>">Revenir sur VINSI</a>
            </div>
            <?php endif; ?>
                
            <div class="ligne_btn txt_centre">
                <?php if (isset($retour) && $retour): ?>
                    <a href="<?php echo url_for('vrac_societe', array('identifiant' => str_replace('COMPTE-', '', $societe->compte_societe))); ?>" class="btn_majeur btn_acces">Mes Déclarations</a>
                <?php endif; ?>
            </div>
            <div class="ligne_btn txt_centre">
                <?php if ($etablissementPrincipal->isCourtier() || $etablissementPrincipal->isNegociant()): ?>
                    <a href="<?php echo url_for('annuaire', array('identifiant' => $etablissementPrincipal->identifiant)); ?>" class="btn_majeur btn_annuaire">Annuaire</a>
                <?php endif; ?>
            </div>
            <br/>
            <br/>
        </div>
    </div>
</div>
<?php
end_slot();

slot('colAide');
$contacts = sfConfig::get('app_teledeclaration_contact_contrat');
$region = $etablissementPrincipal->region;
?>
<div class="bloc_col" id="contrat_aide">
    <h2>Aide</h2>

    <div class="contenu">
        <p>
            En cas de besoin n'hésitez pas à consulter la notice d'aide complète au format pdf.
        </p>

        <a href="#" class="lien_notice">Télécharger la notice</a>


        <h3>Votre contact - mise en marche</h3>

        <ul class="contact"> 
            <li class="nom"><?php echo $contacts[$region]['nom']; ?></li>
            <li class="email"><a href="mailto:<?php echo $contacts[$region]['email']; ?>"><?php echo $contacts[$region]['email']; ?></a></li>
            <li class="telephone"><?php echo $contacts[$region]['telephone']; ?></li>
        </ul>
    </div>
</div>
<?php
end_slot();
?>