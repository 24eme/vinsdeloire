<?php
use_helper('Float');
?>
    
    <!-- #principal -->
    <section id="principal" class="ds">
        <p id="fil_ariane"><a href="<?php echo url_for('ds') ?>">Page d'accueil</a> 
            &gt; <a href="<?php echo url_for('ds_etablissement',array('identifiant' => $ds->identifiant)); ?>"><?php echo $ds->declarant->nom; ?></a>
            &gt; <a href="<?php echo url_for("ds_edition_operateur", $ds) ?>">Stocks : consultation & déclaration</a> &gt; <strong>Ajouter un produit</strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <h2>Consulter les stocks d'un opérateur :</h2>
          	<?php include_partial('produitForm', array('ds' => $ds, 'form' => $form)); ?>
		</section>
    </section>
    <!-- fin #principal -->
    
   <?php
slot('colButtons');
?>
<div id="action" class="bloc_col">
    <h2>Action</h2>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('ds'); ?>" class="btn_majeur btn_acces"><span>Retour à l'accueil</span></a>
        </div>
         <div class="btnRetourAccueil">
            <a href="<?php echo url_for('ds_etablissement',array('identifiant' => $ds->identifiant)); ?>" class="btn_majeur btn_acces"><span>Retour à l'historique</span></a>
        </div>
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for("ds_edition_operateur", $ds) ?>" class="btn_majeur btn_acces"><span>Retour à l'édition</span></a>
        </div>
      </div>
</div>
<?php
end_slot();
?>
<script type="text/javascript">
$(document).ready(function () {
		$( "#<?php echo $form['hashref']->renderId() ?>" ).combobox();
});
</script>
