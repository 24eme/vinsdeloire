<?php
/* Fichier : soussigneSuccess.php
 * Description : Fichier php correspondant à la vue de vrac/nouveau-soussigne
 * Formulaire d'enregistrement de la partie soussigne des contrats (modification de contrat)
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 29-05-12
 */
//if (!$isTeledeclarationMode) :
if ($nouveau) :
    ?>
    <script type="text/javascript">
        $(document).ready(function()
        {
            init_ajax_nouveau();
        });
    </script>
    <?php
else :
    $numero_contrat = $form->getObject()->numero_contrat;
    ?>
    <script type="text/javascript">
        $(document).ready(function()
        {
            ajaxifyAutocompleteGet('getInfos', {autocomplete: '#vendeur_choice', 'numero_contrat': '<?php echo $numero_contrat; ?>'}, '#vendeur_informations');
            ajaxifyAutocompleteGet('getInfos', {autocomplete: '#acheteur_choice', 'numero_contrat': '<?php echo $numero_contrat; ?>'}, '#acheteur_informations');
            ajaxifyAutocompleteGet('getInfos', {autocomplete: '#mandataire_choice', 'numero_contrat': '<?php echo $numero_contrat; ?>'}, '#mandataire_informations');
            majMandatairePanel();
            //$('#vrac_vendeur_famille_viticulteur').attr('checked','checked');
            //$('#vrac_acheteur_famille_negociant').attr('checked','checked');
        });
    </script>
<?php
endif;
//endif;

$urlForm = null;

if (($form->getObject()->isNew() && !isset($isTeledeclarationMode)) || ($form->getObject()->isNew() && !$isTeledeclarationMode)) :
    $urlForm = url_for('vrac_nouveau');
elseif ($form->getObject()->isNew() && isset($isTeledeclarationMode) && $isTeledeclarationMode) :
    if (isset($choixEtablissement) && $choixEtablissement):
        $urlForm = url_for('vrac_nouveau', array('choix-etablissement' => $choixEtablissement));
    else:
        $urlForm = url_for('vrac_nouveau', array('etablissement' => $etablissementPrincipal->identifiant));
    endif;
else :
    $urlForm = url_for('vrac_soussigne', $vrac);
endif;
?>
<section id="principal">
    <?php include_partial('headerVrac', array('vrac' => $form->getObject(), 'compte' => $compte, 'actif' => 1, 'urlsoussigne' => $urlForm)); ?>
    <div id="contenu_etape">
        <form id="vrac_soussigne" method="post" action="<?php echo $urlForm; ?>">   
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>

            <div id="vendeur" class="block_overlay">   
                <!--  Affichage des vendeurs disponibles  -->
                <?php if ($isTeledeclarationMode): ?>
                    <?php $url_ajout_vendeur = url_for('vrac_annuaire', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissementPrincipal->identifiant, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'vendeur')); ?>
                    <div id="vendeur_choice" class="section_label_maj section_label_maj_teledeclaration" >
                        <label>Vendeur</label><br />                        
                        <?php echo $form['vendeur_identifiant']->renderLabel(null, array('class' => 'label_soussigne_identifiant')); ?>
                        <?php echo $form['vendeur_identifiant']->render(array('class' => 'autocomplete combobox', 'data-btn-ajout-txt' => 'Ajouter un vendeur', 'data-url' => $url_ajout_vendeur)); ?>
                        <?php echo $form['vendeur_identifiant']->renderError(); ?>
                        <br /><br />
                    </div>
                <?php else: ?>
                    <div id="vendeur_choice" class="section_label_maj" >
                        <?php echo $form['vendeur_identifiant']->renderLabel(); ?>
                        <?php echo $form['vendeur_identifiant']->render(array('class' => 'autocomplete')); ?>
                        <?php echo $form['vendeur_identifiant']->renderError(); ?>
                        <br /><br />
                    </div>
                <?php endif; ?>
                <!--  Affichage des informations sur le vendeur sélectionné AJAXIFIED -->
                <div id="vendeur_informations">
                    <?php
                    $vendeurArray = array();
                    $vendeurArray['vendeur'] = $form->vendeur;
                    $vendeurArray['vendeur'] = ($nouveau) ? $form->getObject()->getVendeurObject() : $form->getObject()->getVendeurObject();
                    $vendeurArray['isTeledeclarationMode'] = $isTeledeclarationMode;
                    include_partial('vendeurInformations', $vendeurArray);
                    ?>
                </div>
                <div class="btnModification">
                    <a id="vendeur_annulation_btn" class="btn_majeur btn_annuler" style="display: none;">Retour</a>
                    <?php if (!$isTeledeclarationMode): ?>
                        <a id="vendeur_modification_btn" class="btn_majeur btn_modifier">Modifier</a>
                    <?php endif; ?>
                </div>
            </div>           
            <!--  Affichage des acheteurs disponibles  -->
            <div id="acheteur" class="block_overlay"> 

                <?php if (!$isTeledeclarationMode && !isset($isAcheteurResponsable)): ?>
                    <div id="acheteur_choice" class="section_label_maj">
                        <?php echo $form['acheteur_identifiant']->renderLabel(); ?>
                        <?php echo $form['acheteur_identifiant']->render(); ?><?php echo $form['acheteur_identifiant']->renderError(); ?>

                    </div>
                <?php endif; ?>

                <?php if ($isTeledeclarationMode && !$isAcheteurResponsable): ?>
                    <?php $url_ajout_acheteur = url_for('vrac_annuaire', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissementPrincipal->identifiant, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'acheteur')); ?>
                    <div id="acheteur_choice" class="section_label_maj section_label_maj_teledeclaration" >
                        <label>Acheteur</label><br />
                        <?php echo $form['acheteur_identifiant']->renderLabel(null, array('class' => 'label_soussigne_identifiant')); ?>
                        <?php echo $form['acheteur_identifiant']->render(array('class' => 'autocomplete combobox', 'data-btn-ajout-txt' => 'Ajouter un vendeur', 'data-url' => $url_ajout_acheteur)); ?>                        
                        <?php echo $form['acheteur_identifiant']->renderError(); ?>
                    </div>
                <?php endif; ?>
                <?php
                if ($isTeledeclarationMode && $isAcheteurResponsable) :
                    $identifiantAcheteur = (isset($etablissement)) ? $etablissement : $vrac->acheteur_identifiant;
                    ?>
                    <div id="acheteur_choice" class="section_label_maj section_label_maj_teledeclaration">
                        <label >Acheteur :</label>
                        <input type="hidden" name="vrac[acheteur_identifiant]" value="<?php echo "ETABLISSEMENT-" . $identifiantAcheteur; ?>"/>
                    </div>
                <?php endif; ?> 

                <!--  Affichage des informations sur l'acheteur sélectionné AJAXIFIED -->
                <div id="acheteur_informations">
                    <?php
                    $acheteurArray = array();
                    $acheteurArray['acheteur'] = $form->acheteur;
                    $acheteurArray['acheteur'] = ($nouveau) ? $form->getObject()->getAcheteurObject() : $form->getObject()->getAcheteurObject();
                    $acheteurArray['isTeledeclarationMode'] = $isTeledeclarationMode;
                    include_partial('acheteurInformations', $acheteurArray);
                    ?>
                </div>
                <div class="btnModification">
                    <a id="acheteur_annulation_btn" class="btn_majeur btn_annuler" style="display: none;">Retour</a>
                    <?php if (!$isTeledeclarationMode): ?>
                        <a id="acheteur_modification_btn" class="btn_majeur btn_modifier">Modifier</a>
                    <?php endif; ?>
                </div>
            </div>

            <!--  Affichage des courtiers disponibles  -->
            <?php if ($isTeledeclarationMode): ?>
                <?php if (!$isAcheteurResponsable): ?>
                    <?php $url_ajout_courtier = url_for('vrac_annuaire_commercial', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissementPrincipal->identifiant)); ?>
                    <div id="teledeclaration_courtier" > 
                        <?php if ($isCourtierResponsable): ?>
                            <div id="" class="section_label_maj">
                                Ajouter un interlocuteur commercial : 
                                <input type="checkbox" id="teledeclaration_courtier_interlocuteur_commercial_show">
                            </div>
                        <?php endif; ?>
                        <div id="teledeclaration_courtier_interlocuteur_commercial" class="section_label_maj" <?php echo ($isCourtierResponsable) ? 'style="display:none;"' : '' ?>  >
                            <?php if (isset($form['commercial'])): ?>
                                <label>Courtier</label><br />
                                <?php echo $form['commercial']->renderError(); ?>
                                <?php echo $form['commercial']->renderLabel(null, array('class' => 'label_soussigne_identifiant')) ?>
                                <?php echo $form['commercial']->render(array('class' => 'autocomplete combobox', 'data-btn-ajout-txt' => 'Ajouter un courtier', 'data-url' => $url_ajout_courtier)) ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($form->getObject()->getMandataireExist()): ?>
                        <input id="vrac_mandataire_exist" type="hidden" value="1" name="vrac[mandataire_exist]">                
                        <input id="vrac_mandataire_identifiant" type="hidden" value="ETABLISSEMENT-<?php echo $form->getObject()->getMandataireIdentifiant(); ?>" name="vrac[mandataire_identifiant]">
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>

                <div id="interne" class="block_overlay">            

                    <?php echo $form['interne']->render(); ?>
                    <?php echo $form['interne']->renderLabel(); ?>
                    <?php echo $form['interne']->renderError(); ?>
                </div>

                <div id="has_mandataire" class="block_overlay">            
                    <?php echo $form['mandataire_exist']->render(); ?>
                    <?php echo $form['mandataire_exist']->renderLabel(); ?>
                    <?php echo $form['mandataire_exist']->renderError(); ?>
                </div>
                <div id="mandataire" class="block_overlay">     
                    <div id="mandatant" class="section_label_strong" >
                        <?php echo $form['mandatant']->renderError(); ?>
                        <?php echo $form['mandatant']->renderLabel() ?>
                        <?php echo $form['mandatant']->render() ?>        
                    </div>

                    <div id="mandataire_choice" class="section_label_maj">
                        <?php echo $form['mandataire_identifiant']->renderError(); ?>
                        <?php echo $form['mandataire_identifiant']->renderLabel() ?>
                        <?php echo $form['mandataire_identifiant']->render() ?>
                        <?php if (isset($form['commercial'])): ?>
                            <br /><br />
                            <?php echo $form['commercial']->renderError(); ?>
                            <?php echo $form['commercial']->renderLabel() ?>
                            <?php echo $form['commercial']->render() ?>
                            <br /><br />
                            <a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire_commercial', array('numero_contrat' => $form->getObject()->_id, 'sf_subject' => $form->getObject(), 'identifiant' => $etablissementPrincipal->identifiant)) ?>">Ajouter un contact</a>
                        <?php endif; ?>
                    </div>

                    <!--  Affichage des informations sur le mandataire sélectionné AJAXIFIED -->
                    <div id="mandataire_informations">
                        <?php
                        $mandataireArray = array();
                        $mandataireArray['mandataire'] = $form->mandataire;
                        if (!$nouveau)
                            $mandataireArray['mandataire'] = (!$hasmandataire) ? $mandataireArray['mandataire'] : $form->getObject()->getMandataireObject();
                        $mandataireArray['isTeledeclarationMode'] = $isTeledeclarationMode;
                        include_partial('mandataireInformations', $mandataireArray);
                        ?>    
                    </div>
                    <div class="btnModification">
                        <a id="mandataire_annulation_btn" class="btn_majeur btn_annuler" style="display: none;" href="#">Retour</a>
                        <a id="mandataire_modification_btn" class="btn_majeur">Modifier</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="btn_etape block_overlay" id="ligne_btn">
                <?php if ($nouveau): ?>
                    <a href="<?php echo url_for('vrac'); ?>" class="btn_majeur btn_annuler"><span>Annuler la saisie</span></a>
                <?php endif; ?>

                <button id="btn_soussigne_submit" class="btn_etape_suiv" type="submit"><span>Etape Suivante</span></button>
            </div>

        </form>
    </div>
</section>
<?php if ($isTeledeclarationMode): ?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".ajouter_annuaire").click(function() {
            $("#vrac_soussigne").attr('action', $(this).attr('href'));
            $("#vrac_soussigne").submit();
            return false;
        });
        $("div#acheteur_choice input.ui-autocomplete-input").val('<?php echo $etablissementPrincipal->_id; ?>');


<?php if ($isCourtierResponsable): ?>
            initTeledeclarationCourtierSoussigne();
<?php endif; ?>
    });
</script>
<?php endif; ?>
<?php
if ($isTeledeclarationMode):
    include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));
else:
    slot('colApplications');
    /*
     * Inclusion du panel de progression d'édition du contrat
     */
    if (!$contratNonSolde)
        include_partial('contrat_progression', array('vrac' => $vrac));

    /*
     * Inclusion des Contacts
     */
    end_slot();
endif;
?>
 