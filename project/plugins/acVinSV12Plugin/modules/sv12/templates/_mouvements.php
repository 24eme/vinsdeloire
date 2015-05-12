<?php use_helper('Float'); ?> 
<?php use_helper('Date'); ?>
<?php use_helper('Mouvement') ?>

<?php if (count($mouvements) > 0): ?>

    <?php if (isset($hamza_style)) : ?>
        <?php
        include_partial('global/hamzaStyle', array('table_selector' => '#table_mouvements',
            'mots' => mouvement_get_words($mouvements),
            'consigne' => "Saisissez un produit, un numéro de contrat, un viticulteur ou un type (moût / raisin / vrac) :"))
        ?>
    <?php endif; ?>
    <fieldset id="table_mouvements">
        <table class="table_recap">
            <thead>
                <tr>
                    <th>Date de modification</th>
                    <th>Contrat</th>
                    <th>Produit</th>
                    <th>Volume</th>

                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
    <?php foreach ($mouvements as $mouvement) :
        ?>   
                <tr id="<?php echo mouvement_get_id($mouvement) ?>" class="<?php if ($i % 2 != 0) echo 'alt'; if ($mouvement->facturable) echo " facturable"; ?>">
                        <td>
                            <a title="Saisi le <?php echo format_date($mouvement->date_version, 'D') ?>" href="<?php echo url_for('redirect_visualisation', array('id_doc' => $mouvement->doc_id)) ?>"><?php echo acCouchdbManager::getClient($mouvement->type)->getLibelleFromId($mouvement->doc_id) ?><?php echo ($mouvement->version) ? ' ('.$mouvement->version.')' : '' ?></a>
                        </td>
                        <td>
                            <?php if ($mouvement->vrac_numero) { ?>
	  <a href="<?php echo url_for(array('sf_route' => 'vrac_visualisation', 'numero_contrat' => $mouvement->vrac_numero)) ?>"><?php echo sprintf("%s, n°%s, %s", $mouvement->type_libelle, $mouvement->numero_archive, $mouvement->vrac_destinataire); ?></a> 
                            <?php
                            } else if ($mouvement->vrac_destinataire) {
                                echo "SANS CONTRAT " . sprintf("(%s, %s)", $mouvement->type_libelle, $mouvement->vrac_destinataire);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td>
                    <?php echo $mouvement->produit_libelle; ?>
                        </td>
                        <td <?php echo ($mouvement->volume > 0) ? ' class="positif"' : 'class="negatif"'; ?> >
        <?php echoSignedFloat($mouvement->volume * -1); ?>
                        </td>
                    </tr>
        <?php
    endforeach;
    ?>
            </tbody>
        </table> 
    </fieldset>
<?php else: ?>
    <p>Pas de mouvements</p>
<?php endif; ?>