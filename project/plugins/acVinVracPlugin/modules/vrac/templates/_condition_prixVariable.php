<?php
/* Fichier : _condition_prixvariable.php
 * Description : Fichier php correspondant à une vue partielle de vrac/XXXXXXXXXXX/condition
 * Formulaire concernant la parti prix variable pour les conditions du contrat
 * Affiché si et seulement si type de contrat = 'pluriannuel' et partie de prix variable = 'Oui'
 * Auteur : Petit Mathurin - mpetit[at]actualys.com
 * Version : 1.0.0 
 * Derniere date de modification : 28-05-12
 */

use_helper('Vrac');
use_helper('Float');
 ?>
<div id="prix_variable" <?php echo ($displayPrixVariable)? '' : 'style="display:none;"'; ?> class="section_label_maj">
    <label>Prix variable</label>
    <div class="bloc_form">
        <!--  Affichage des la part variable sur la quantité du contrat  -->
        <div id="part_variable" class="ligne_form ligne_form_alt">
                <span>
                    <?php echo $form['part_variable']->renderError() ?>
                    <?php echo $form['part_variable']->renderLabel() ?>
                    <?php echo $form['part_variable']->render() ?> <span>% (50% max)</span>
                </span>
        </div>
    </div>
</div>
<?php if(isset($form['cvo_nature']) || isset($form['cvo_repartition'])): ?>
<div class="section_label_maj">
    <label>CVO appliquée</label>
    <div class="bloc_form">
        <!--  Affichage de la nature du contrat  -->
        <?php if(isset($form['cvo_nature'])): ?>
        <div id="cvo_nature" class="ligne_form" >
            <span>
                <?php echo $form['cvo_nature']->renderError() ?> 
                <?php echo $form['cvo_nature']->renderLabel() ?>
                <?php echo $form['cvo_nature']->render() ?>
            </span>   
        </div>
        <?php endif; ?>

        <?php if(isset($form['cvo_repartition'])): ?>
        <!--  Affichage de la repartition (vendeur/acheteur) pour le paiement de la CVO  -->
        <div id="taux_variation" class="ligne_form ligne_form_alt" >
            <span>
                <?php echo $form['cvo_repartition']->renderError() ?>
                <?php echo $form['cvo_repartition']->renderLabel() ?>
                <?php echo $form['cvo_repartition']->render() ?>
            </span>
        </div>

<?php
$taux = $form->getObject()->getDroitCVO()->taux;
$volume = $form->getObject()->volume_propose;
?>
        <!-- CVO facturée vendeur  -->
        <div id="cvo_facturee_vendeur" class="ligne_form" >
            <span>
                <label>CVO facturée (vendeur)</label>
                <span id="prix_facturee_vendeur"><?php sprintFloatFr($taux / 2); ?></span>&nbsp;€/hl
                (soit <span  id="cvo_totale_vendeur"> <?php sprintFloatFr($taux * $volume / 2); ?></span>&nbsp;€)
            </span>
        </div>


        <!-- CVO facturée acheteur -->
        <div id="cvo_facturee_acheteur" class="ligne_form ligne_form_alt" >
            <span>
                <label>CVO facturée (acheteur)</label>
	        <span  id="prix_facturee_acheteur"><?php sprintFloatFr($taux / 2); ?></span>&nbsp;€/hl
		(soit <span  id="cvo_totale_acheteur"> <?php sprintFloatFr($taux * $volume / 2); ?></span>&nbsp;€)
            </span>
        </div>
    </div>
</div>
<script type="text/javascript">
    var cvo_taux = <?php echo $taux ; ?>;
    var cvo_volume = <?php echo $volume; ?>;

    $('#vrac_cvo_repartition').change(function() {
        update_cvo_repartition();
    });

    function update_cvo_repartition() {
        switch($('#vrac_cvo_repartition').val())
          {
          case "100":
        $('#prix_facturee_vendeur').html((cvo_taux).toFixed(2));
        $('#prix_facturee_acheteur').html("0.00");
        $('#cvo_totale_vendeur').html((cvo_taux * cvo_volume).toFixed(2));
        $('#cvo_totale_acheteur').html("0.00");
        break;
          case "50":
        $('#prix_facturee_vendeur').html((cvo_taux / 2).toFixed(2));
        $('#prix_facturee_acheteur').html((cvo_taux / 2).toFixed(2));
        $('#cvo_totale_vendeur').html((cvo_taux * cvo_volume / 2).toFixed(2));
        $('#cvo_totale_acheteur').html((cvo_taux * cvo_volume / 2).toFixed(2));
        break;
          default:
        $('#prix_facturee_vendeur').html("0.00");
        $('#prix_facturee_acheteur').html("0.00");
        $('#cvo_totale_vendeur').html("0.00");
        $('#cvo_totale_acheteur').html("0.00");
        break;
          }
    }
    update_cvo_repartition();
</script>
<?php endif; ?>
<?php endif; ?>
