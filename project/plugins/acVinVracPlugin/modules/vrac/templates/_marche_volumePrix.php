<?php use_helper('Float') ?>

<?php
/* Fichier : _marche_volumePrix.php
 * Description : Fichier php correspondant à la vue partielle de vrac/XXXXXXXXXXX/marche
 * Partie du formulaire permettant le choix des volumes et des prix, affichage du total
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 28-05-12
 */
$isRaisin = $form->getObject()->exist('type_transaction') && $form->getObject()->type_transaction == VracClient::TYPE_TRANSACTION_RAISINS;
$isMoutOuVrac = $form->getObject()->exist('type_transaction') && 
                         ($form->getObject()->type_transaction == VracClient::TYPE_TRANSACTION_VIN_VRAC
                         || $form->getObject()->type_transaction == VracClient::TYPE_TRANSACTION_MOUTS);
$isBouteille = $form->getObject()->exist('type_transaction') && ($form->getObject()->type_transaction == VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE);    

?>

<script>
var value2bouteilleContenance = <?php echo json_encode(VracClient::getInstance()->getContenances()); ?>;
function getBouteilleContenance($value) {
    return value2bouteilleContenance[$value];
}
</script>

<div id="vrac_marche_volumePropose" class="section_label_maj">
    <label>Volumes proposés</label>
    <!--  Affichage des contenances disponibles (seulement s'il s'agit de vins en bouteilles)  -->
    <div class="bloc_form vrac_volume_propose_panel" min-height:100px; >
        <?php echo $form['bouteilles_contenance_libelle']->renderError() ?>
        <div id="contenance" class="bouteilles_contenance_libelle ligne_form" 
            <?php echo ($isBouteille)? '' : 'style="display: none;"' ?> >
            <span> <?php echo $form['bouteilles_contenance_libelle']->renderLabel() ?> </span>
            <?php echo $form['bouteilles_contenance_libelle']->render() ?>
        </div>

        <!--  Affichage des volumes disponibles variables selon le type de transaction choisi  -->
        <div id="volume" class="ligne_form">
            
            <?php echo $form['bouteilles_quantite']->renderError() ?>
            <div class="bouteilles_quantite "
                 <?php echo ($isBouteille)? '' : 'style="display: none;"' ?> >                
                <span><?php echo $form['bouteilles_quantite']->renderLabel() ?></span>
                <?php echo $form['bouteilles_quantite']->render() ?>
                <span id="volume_unite_total" class="unite"></span>
            </div>
            
            <?php echo $form['jus_quantite']->renderError() ?>
            <div class="jus_quantite "
                 <?php echo ($isMoutOuVrac)? '' : 'style="display: none;"' ?> >
                <span>  <?php echo $form['jus_quantite']->renderLabel() ?></span>
                <?php echo $form['jus_quantite']->render() ?>
                <span id="volume_unite_total" class="unite"></span>
            </div>
            
            <?php echo $form['raisin_quantite']->renderError() ?>
            <div class="raisin_quantite "
                 <?php echo ($isRaisin)? '' : 'style="display: none;"' ?> >
                <span><?php echo $form['raisin_quantite']->renderLabel() ?></span>
                <?php echo $form['raisin_quantite']->render() ?>
                <span id="volume_unite_total" class="unite"></span>
            </div>
            <div>
                <input type="hidden" id="volume_total"/>
            </div>
        </div>
    </div>
</div>

<!--  Affichage du prix unitaire variables selon le type de transaction choisi -->
<div id="prixInitialUnitaire" class="section_label_maj">
    <?php echo $form['prix_initial_unitaire']->renderLabel() ?>
    <?php echo $form['prix_initial_unitaire']->render() ?>       
    <span id="prix_initial_unitaire_unite" class="unite"></span>
    <span id="prix_initial_unitaire_hl" class="small"></span>
    <?php echo $form['prix_initial_unitaire']->renderError(); ?>
</div>
           
<?php if ($form->getObject()->hasPrixVariable()): ?>
<div id="prixUnitaire" class="section_label_maj">
    <?php echo $form['prix_unitaire']->renderLabel() ?>
    <?php echo $form['prix_unitaire']->render() ?>       
    <span id="prix_unitaire_unite" class="unite"></span>
    <span id="prix_unitaire_hl" class="small"></span>
    <?php echo $form['prix_unitaire']->renderError(); ?>
</div>
<?php endif; ?>     

<!--  Affichage du prix total (quantité x nbproduit)  -->
<div class="bloc_form" >
    <div id="prixInitialTotal" class="ligne_form">
        <?php if ($form->getObject()->hasPrixVariable()): ?>
        <span><label>Prix initial total</label>
        <?php else: ?>
        <span><label>Prix total</label>
        <?php endif; ?>
        <span id="vrac_prix_initial_total" class="unite"><?php echoFloat($form->getObject()->prix_initial_total) ?></span>
        <span id="vrac_prix_initial_unite" class="small"><?php if(!is_null($form->getObject()->prix_initial_total)):?>€<?php endif; ?></span>  
        </span>
    </div>
    <?php if ($form->getObject()->hasPrixVariable()): ?>
    <div id="prixTotal" class="ligne_form">
        <span><label>Prix définitif total</label>
            <span id="vrac_prix_total" class="unite"><?php echoFloat($form->getObject()->prix_total) ?></span>
            <span id="vrac_prix_unite" class="small"><?php if(!is_null($form->getObject()->prix_total)):?>€<?php endif; ?></span>
        </span>
    </div>
    <?php endif; ?>
</div>
                