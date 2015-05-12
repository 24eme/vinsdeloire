<?php use_helper('Float') ?>
<?php use_helper('Date') ?>
<?php use_helper('DRM') ?>

<?php if(count($produits->getRawValue()) > 0): ?>
<div class="section_label_maj" id="calendrier_drm">
   <form method="post">
   <?php echo $formCampagne->renderGlobalErrors() ?>
   <?php echo $formCampagne->renderHiddenFields() ?>
   <?php echo $formCampagne; ?> <input class="btn_majeur btn_vert" type="submit" value="changer"/>
   </form>

    <?php if(isset($hamza_style)) : ?>
    <?php include_partial('global/hamzaStyle', array('table_selector' => '#table_stocks', 
                                                     'mots' => produit_get_words($produits),
                                                     'consigne' => "Saisissez un mois ou un produit :")) ?>
    <?php endif; ?>

    <?php if($vigilance): ?>
    <div id="points_vigilance">
        <ul>
            <li class="warning">
                Les informations fournies ci-dessous sont partielles car une DRM est en cours de saisie pour cette campagne
            </li>
        </ul>
    </div>
    <?php endif; ?>

    <table id="table_stocks" class="table_recap">
        <thead>
            <tr>
                <th>Mois</td>
                <th style="width: 200px;">Produits</td>
                <th>Stock début de mois</th>
                <th>Entrées</th>
                <th>Sorties (Fact.)</th>
                <th><strong>Stock fin de mois</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach($produits->getRawValue() as $produit): ?>
            <?php $i++; ?>
                    <tr id="<?php echo produit_get_id($produit) ?>" <?php if($i%2!=0) echo ' class="alt"'; ?>>
                        <td><a href="<?php echo url_for('drm_visualisation', array('identifiant' => $etablissement->identifiant, 'periode_version' => DRMClient::getInstance()->buildPeriodeAndVersion($produit->periode, $produit->version)))?>"><?php echo $produit->mois ?></a></td>
                        <td><?php echo $produit->produit_libelle ?></td>
                        <td><strong><?php echoFloat($produit->total_debut_mois) ?></strong></td>
                        <td><?php echoFloat($produit->total_entrees) ?></td>
                        <td><?php echoFloat($produit->total_sorties) ?> (<?php echoFloat($produit->total_facturable) ?>)</td>
                        <td><strong><?php echoFloat($produit->total) ?></strong></td>
                    </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<p> Aucun stocks </p>
<?php endif; ?>