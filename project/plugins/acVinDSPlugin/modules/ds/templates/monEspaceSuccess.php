<?php
use_helper('Float');
?>
<!-- #principal -->
    <section id="principal" class="ds">
        <p id="fil_ariane"><a href="<?php echo url_for('ds') ?>">Page d'accueil</a> &gt; <strong>Stocks : consultation & déclaration</strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <h2>Consulter les stocks d'un opérateur :</h2>
          <?php include_component('ds', 'chooseEtablissement', array('identifiant' => $etablissement->identifiant)); ?>
		  
			<h2>Détail opérateur</h2>
			<?php 
			include_partial('etablissementInformations', array('etablissement' => $etablissement));
			?>
                        
			<?php if(count($dsHistorique)) : ?>
			<h2>Historique des déclarations de stocks</h2>
			<?php  include_partial('dsHistorique', array('dsHistorique' => $dsHistorique)) ?>
			<?php endif; ?>
			
			<?php include_partial('generationFormulairesOperateur', array('etablissement' => $etablissement, 'generationOperateurForm' => $generationOperateurForm)); ?>
			
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
      </div>
</div>
<?php
end_slot();
?>

<script type="text/javascript">
    
    $(document).ready( function()
	{
            $('#generation_ds').bind('click', function()
            {
                $('form#generation_form').submit();
		return false;
            });
        });
    
</script>

