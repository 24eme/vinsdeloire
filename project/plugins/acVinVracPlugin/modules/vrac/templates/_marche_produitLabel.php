<?php
/* Fichier : _marche_produitLabel.php
 * Description : Fichier php correspondant à la vue partielle de vrac/XXXXXXXXXXX/marche
 * Partie du formulaire permettant le choix du produit et du label
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 28-05-12
 */


 $has_domaine = !is_null($form->getObject()->domaine);
 ?>
<?php echo $form['produit']->renderError(); ?> 
<!--  Affichage des produits disponibles (en fonction de la transaction choisie  -->
<div id="produit" class="section_label_maj">   
    <?php echo $form['produit']->renderLabel() ?>
    <?php echo $form['produit']->render() ?>
</div>

<?php echo $form['millesime']->renderError(); ?>
<!--  Affichage des millésimes  -->
<div id="millesime" class="section_label_maj">
    <?php echo $form['millesime']->renderLabel(); ?> 
    <?php echo $form['millesime']->render(); ?>
</div>


<?php echo $form['categorie_vin']->renderError(); ?>
<!--  Affichage du type  -->
<div id="type" class="section_label_maj">
    <?php echo $form['categorie_vin']->renderLabel() ?> 
    <?php echo $form['categorie_vin']->render() ?>    
</div>

<?php echo $form['domaine']->renderError(); ?>
<!--  Affichage du type  -->
<div id="domaine" class="section_label_maj" 
    <?php echo ($form->getObject()->exist('categorie_vin') && ($form->getObject()->categorie_vin == VracClient::CATEGORIE_VIN_DOMAINE))? '' : 'style="display: none;"' ?> >
    <?php echo $form['domaine']->renderLabel() ?> 
    <?php echo $form['domaine']->render() ?>   
</div>

<?php if (isset($form['label'])): ?>
<?php echo $form['label']->renderError(); ?>
<!--  Affichage des label disponibles -->
<div id="label" class="section_label_maj">
    <?php echo $form['label']->renderLabel() ?> 
    <?php echo $form['label']->render() ?>
</div>
<?php endif; ?>