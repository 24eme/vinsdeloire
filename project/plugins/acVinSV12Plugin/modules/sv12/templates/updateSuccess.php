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
<?php if (!$sv12->hasSV12DouaneImported() && $sv12->getSV12DouaneURL()): ?>
<div style="float: right;">
    <a href="<?php echo url_for('sv12_import', $sv12); ?>" class="btn_majeur btn_orange">Importer depuis une SV12</a>
</div>
<?php endif; ?>
<h3>Saisie des volume</h3>
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
                            <th colspan="2">Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="vide">
                            <td colspan="4">Aucun résultat n'a été trouvé pour cette recherche</td>
                        </tr>
                        <?php $last_identifiant = ""; foreach ($sv12->getContratsByVendeur() as $k => $contrat) : ?>
                            <tr id="<?php echo contrat_get_id($contrat) ?>" class="<?php if($contrat->volume){echo "saisi";} ?>">
                                <td<?php if ($contrat->vendeur_identifiant == $last_identifiant){echo ' style="opacity: 0.5;"';} $last_identifiant = $contrat->vendeur_identifiant; ?>>
                                    <?php if ($contrat->vendeur_identifiant): ?><?php echo $contrat->vendeur_nom . ' (' . $contrat->vendeur_identifiant . ')'; ?><?php elseif ($contrat->exist('commentaire')): echo $contrat->commentaire; else: ?>-<?php endif; ?>
                                </td>
                                <td><?php echo $contrat->produit_libelle; ?></td>
                                <td>
                                    <?php if (!$contrat->contrat_numero): ?>
                                        -
                                    <?php else: ?>
                                        <a href="<?php echo url_for(array('sf_route' => 'vrac_visualisation', 'numero_contrat' => $contrat->contrat_numero)) ?>"><?php echo $contrat->numero_archive; ?></a><br/>
                                        <?php echo '('.$contrat->getContratTypeLibelle().',&nbsp;';
                                              $style = '';
                                              if ($contrat->volume_prop && ($contrat->exist('volume_sv12') || $contrat->volume)) {
                                                  $ratio = abs($contrat->volume - $contrat->volume_prop) / $contrat->volume_prop;
                                                  if ($ratio > 0.5) {
                                                      $style = ' style="color: red;font-weight:bold;"';
                                                  }elseif ($ratio > 0.1) {
                                                      $style = ' style="color: orange;font-weight:bold;"';
                                                  }

                                              }
                                              echo '<span'.$style.'>'.$contrat->volume_prop.'&nbsp;hl</span>)'; ?>
                                    <?php endif; ?>
                                </td>
                                <?php
                                    echo '<td>';
                                    if ($contrat->isImportAuto()) {
                                        echo $form[$contrat->getKey()]->render(array('readonly'=> 'readonly', 'style' => 'box-shadow: none; text-align: right', "size" => 8));
                                        echo "</td><td><a href='#' class='aedit'>(E)</a></td>";
                                    }else{
                                        echo $form[$contrat->getKey()]->renderError();
                                        echo $form[$contrat->getKey()]->render(array('style' => 'text-align: right', "size" => 8));
                                        echo "</td><td>";
                                        if ($contrat->volume) {
                                            echo "<a href='#' class='aclear'>(X)</a>";
                                        }
                                        echo "</td>";
                                    }
                                    echo '</td>';
                                ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <script>
                    $(".aclear").click(function() {
                        $(this).html("");
                        $(this).parent().parent().find( "input" ).val("");
                        return false;
                        });
                    $(".aedit").click(function() {
                        $(this).html("");
                        input = $(this).parent().parent().find( "input" );
                        input.removeAttr("readonly");
                        input.attr("style", "text-align: right;");
                        return false;
                        });
                </script>
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

    