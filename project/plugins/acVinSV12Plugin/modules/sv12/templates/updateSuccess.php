<?php use_helper('SV12'); ?>

<!-- #principal -->
<section id="principal" class="sv12">
    <p id="fil_ariane"><a href="<?php echo url_for('sv12') ?>">Page d'accueil</a> &gt; <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>"><?php echo $sv12->declarant->nom ?></a> &gt; <strong><?php echo $sv12 ?></strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <h2>Déclaration SV12</h2>

<!--<p id="num_sv12"><span>N° SV12 :</span> <?php echo $sv12->get('_id') ?></p>-->

        <?php include_partial('negociant_infos', array('sv12' => $sv12)); ?>

        <form name="sv12_update" method="POST" action="<?php echo url_for('sv12_update', $sv12); ?>" >
            <?php
            echo $form->renderHiddenFields();
            echo $form->renderGlobalErrors();
            ?>

            <fieldset id="edition_sv12">
                <legend>Saisie des volume</legend>

                <?php include_partial('global/hamzaStyle', array('table_selector' => '#table_contrats',
                                                                 'mots' => contrat_get_words($sv12->contrats),
                                                                 'consigne' => "Saisissez un produit, un numéro de contrat, un viticulteur ou un type (moût / raisin) :")) ?>

                <!-- <div class="hamza_style">
                    <div class="autocompletion_tags" data-table="#table_contrats" data-source="source_tags">
                        <label>Saisissez le nom d'un viticulteur ou d'une appellation pour effectuer une recherche dans l'historique ci-dessous :</label>

                        <ul id="recherche_sv12_tags" class="tags"></ul>
                        
                        <button class="btn_majeur btn_rechercher" type="button">Rechercher</button>
                        
                    </div>

                    <div class="volumes_vides">
                        <label for="champ_volumes_vides"><input type="checkbox" id="champ_volumes_vides" checked/> Afficher uniquement les volumes non-saisis</label>
                    </div>
                </div> -->
                <table id="table_contrats" class="table_recap">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Viticulteur </th>
                            <th>Produit</th>
                            <th>Contrat</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vide">
                            <td colspan="4">Aucun résultat n'a été trouvé pour cette recherche</td>
                        </tr>
                        <?php foreach ($sv12->contrats as $contrat) : ?> 
                            <tr id="<?php echo contrat_get_id($contrat) ?>" class="<?php if($contrat->volume){echo "saisi";} ?>">
                                <td><?php if ($contrat->vendeur_identifiant): ?><?php echo $contrat->vendeur_nom . ' (' . $contrat->vendeur_identifiant . ')'; ?><?php else: ?>-<?php endif; ?></td>
                                <td><?php echo $contrat->produit_libelle; ?></td>	
                                <td>
                                    <?php if (!$contrat->contrat_numero): ?>
                                        -
                                    <?php else: ?>
                                        <a href="<?php echo url_for(array('sf_route' => 'vrac_visualisation', 'numero_contrat' => $contrat->contrat_numero)) ?>"><?php echo $contrat->numero_archive; ?></a>
                                        <?php echo sprintf('(%s,&nbsp;%s&nbsp;hl)', $contrat->getContratTypeLibelle(), $contrat->volume_prop); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    echo $form[$contrat->getKey()]->renderError();
                                    echo $form[$contrat->getKey()]->render();
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table> 
            </fieldset>
<input type="submit" style="display: none"/>
            <fieldset><input id="addproduit" name="addproduit" type="submit" class="btn_majeur btn_orange" value="Ajouter un produit"/></fieldset>

            <fieldset id="commentaire_sv12">
                <legend>Commentaires</legend>
                <textarea></textarea>
            </fieldset>

            <div class="btn_etape">
                <button class="btn_etape_suiv" type="submit"><span>Suivant</span></button>
            </div>
        </form>
    </section>
    <!-- fin #contenu_etape -->
</section>
<?php
slot('colButtons');
?>
<div id="action" class="bloc_col">
    <h2>Action</h2>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('sv12'); ?>" class="btn_majeur btn_acces"><span>Retour à l'accueil</span></a>
        </div>
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('sv12_etablissement', $sv12->getEtablissementObject()) ?>" class="btn_majeur btn_acces"><span>Historique opérateur</span></a>
        </div>
    </div>
</div>
<?php
end_slot();
?>

    