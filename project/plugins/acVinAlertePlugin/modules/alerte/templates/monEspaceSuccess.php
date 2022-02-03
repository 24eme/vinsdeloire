<!-- #principal -->
    <section id="principal" class="alerte">
        <p id="fil_ariane"><strong>Page d'accueil</strong></p>

        <!-- #contenu_etape -->
        <section id="contenu_etape">

            <form action="<?php echo url_for('alerte_modification_statuts', array('retour' => 'etablissement')); ?>" method="post" >
            <div id="toutes_alertes">
                    <h2>Les alertes de <?php echo $etablissement->nom; ?></h2>

                    <?php
                    use_helper('Date');
                    $statutsWithLibelles = AlerteClient::getStatutsWithLibelles();
                    ?>
                    <div>
                    <p>Liste des alertes <?php if (!$has_fermee) { echo "non fermée";}?> :</p>
                    </div>
                    <?php if (!count($alertesEtablissement)): ?>
                        <div>
                            <span>
                                Aucune alerte <?php if (!$has_fermee) { echo "non fermée";} ?> pour cet opérateur
                            </span>
                        </div>

                    <?php else: ?>
                        <table class="table_recap table_selection">
                            <thead>
                                <tr>
                                    <th class="selecteur"><input type="checkbox" /></th>
                                    <th>Date du statut</th>
                                    <th>Statut</th>
                                    <th>Type d'alerte</th>
                                    <th>Document concerné</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alertesEtablissement as $alerte) :

                                    $document_link = link_to($alerte->value[AlerteRechercheView::VALUE_LIBELLE_DOCUMENT], 'redirect_visualisation', array('id_doc' => $alerte->value[AlerteRechercheView::VALUE_ID_DOC]));
                                    if(($alerte->key[AlerteRechercheView::KEY_TYPE_ALERTE] == AlerteClient::DRM_MANQUANTE) || ($alerte->key[AlerteRechercheView::KEY_TYPE_ALERTE] == AlerteClient::DRA_MANQUANTE)){
                                                   $document_link = link_to($alerte->value[AlerteRechercheView::VALUE_LIBELLE_DOCUMENT], 'drm_etablissement', array('identifiant' => $alerte->key[AlerteRechercheView::KEY_IDENTIFIANT_ETB], 'campagne' => $alerte->key[AlerteRechercheView::KEY_CAMPAGNE]));
                                                }
                                                $styleRow = "";
                                            $classRow = "";
                                            if($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_FERME){
                                                $styleRow = 'style="opacity: 0.5"';
                                            }
                                            if($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_EN_SOMMEIL){
                                                $styleRow = 'style="opacity: 0.5"';
                                            }
                                            if(($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_A_RELANCER) || ($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_A_RELANCER_AR)){
                                                $classRow = 'statut_solde';
                                            }
                                            if($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_EN_ATTENTE_REPONSE){
                                                $classRow = 'statut_non-solde';
                                            }
                                             if($alerte->key[AlerteRechercheView::KEY_STATUT] == AlerteClient::STATUT_EN_ATTENTE_REPONSE_AR){
                                                 $classRow = 'statut_annule';
                                             }

                                ?>
                                    <tr class="<?php echo $classRow; ?>" <?php echo $styleRow; ?> >
                                        <td class="selecteur">
                                            <?php echo $modificationStatutForm[$alerte->id]->renderError(); ?>
                                            <?php echo $modificationStatutForm[$alerte->id]->render() ?>
                                        </td>
                                        <td>
                                            <?php echo format_date($alerte->value[AlerteRechercheView::VALUE_DATE_MODIFICATION], 'dd/MM/yyyy'); ?>
                                            (Ouv.: <?php echo format_date($alerte->value[AlerteRechercheView::VALUE_DATE_CREATION], 'dd/MM/yyyy'); ?>)
                                        </td>
                                        <td><?php echo $statutsWithLibelles[$alerte->key[AlerteRechercheView::KEY_STATUT]]; ?></td>
                                        <td><?php
                                    echo link_to(AlerteClient::$alertes_libelles[$alerte->key[AlerteRechercheView::KEY_TYPE_ALERTE]], 'alerte_modification', array('type_alerte' => $alerte->key[AlerteRechercheView::KEY_TYPE_ALERTE],
                                        'id_document' => $alerte->value[AlerteRechercheView::VALUE_ID_DOC]));
                                            ?></td>
                                        <td><?php echo $document_link; ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                                ?>
                            </tbody>
                        </table>
            <?php endif; ?>

            </div>
            <div id="modification_alerte">
                <h2>Modification des alertes sélectionnées</h2>
                <?php
                echo $modificationStatutForm->renderHiddenFields();
                echo $modificationStatutForm->renderGlobalErrors();
                ?>

                <div class="bloc_form">
                    <div class="ligne_form">
                        <?php echo $modificationStatutForm['statut_all_alertes']->renderError(); ?>
                        <?php echo $modificationStatutForm['statut_all_alertes']->renderLabel() ?>
                        <?php echo $modificationStatutForm['statut_all_alertes']->render() ?> 
                    </div>
                    <div class="ligne_form ligne_form_alt">
                        <?php echo $modificationStatutForm['commentaire_all_alertes']->renderError(); ?>
                        <?php echo $modificationStatutForm['commentaire_all_alertes']->renderLabel() ?>
                        <?php echo $modificationStatutForm['commentaire_all_alertes']->render() ?> 
                    </div>
                </div>

                <div class="btn_form">
                    <button type="submit" id="alerte_valid" class="btn_majeur btn_modifier">Modifier</button>
                </div>
            </div>
            </form>

        </section>
        <!-- fin #contenu_etape -->
    </section>
    <!-- fin #principal -->
<?php
slot('colButtons');
?>
<div id="action" class="bloc_col">
    <h2>Action</h2>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('alerte'); ?>" class="btn_majeur btn_acces"><span>Retour à l'accueil</span></a>
        </div>
    </div>
</div>
<?php
end_slot();
?>
