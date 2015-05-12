<?php
use_helper('Float');

?>
<form id="" action="<?php echo url_for('ds_edition_operateur', $ds); ?>" method="post">
<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
?>
<fieldset><p><input id="saisie_vci_resqual" type="checkbox"><label for="saisie_vci_resqual">Saisir des réserves qualitatives ou des VCI</label></p></fieldset>
    <fieldset id="dsEdition">
        <table id="ds_edition_table" class="table_recap">
        <thead>
            <tr>
                <th colspan="2">Produits</th>
                <th>Volume saisi</th>
                <th class="colonne_vci">VCI</th>
                <th class="colonne_reservequalitative">Réserve qual.</th>
            </tr>
        </thead>
        <tbody class="ds_edition_tableBody">
            <?php 
            foreach ($declarations as $key => $declaration){
                    $prod_vol = '';
                    if($declaration->stock_initial) 
		      $prod_vol = getArialFloat($declaration->stock_initial);
                    include_partial('item',array('form' => $form, 'key' => $key, 'ds_origine' => DRMClient::getInstance()->getLibelleFromId($declaration->getDocument()->drm_origine), 'declaration' => $declaration, 'prod_libelle' => $declaration->produit_libelle, 'prod_vol' => $prod_vol));
               
            }
    ?>
        </tbody>
        </table>

<input type="submit" style="display: none"/>
		
<script><!--
  $('.colonne_vci').hide();
  $('.colonne_reservequalitative').hide();
  $('#saisie_vci_resqual').change(function() {
      if ($(this).is(':checked')) {
	$('.colonne_vci').show();
	$('.colonne_reservequalitative').show();
      }else{
	$('.colonne_vci').hide();
	$('.colonne_reservequalitative').hide();
      }
    })
--></script>

  <input type="submit" class="btn_majeur btn_orange" name="addproduit" value="Ajouter un produit"/>

        <div id="commentaires" class="section_label_maj">
            <label>
                <?php echo $form['commentaire']->renderLabel() ?>
            </label>
            <div class="bloc_form">
                <?php echo $form['commentaire']->renderError() ?>       
                <?php echo $form['commentaire']->render() ?>
            </div>
        </div> 
        
		<div class="btn_etape">
			<a href="<?php echo url_for('ds_etablissement',array('identifiant' => $ds->identifiant)); ?>" class="btn_etape_prec"><span>Annuler</span></a> 
			<button type="submit" id="ds_declaration_valid" class="btn_majeur btn_valider ds_declaration_addTemplate">Suivant</button>
		</div>
</fieldset>
</form>

