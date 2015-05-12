<?php
use_helper('Float');
?>    
    <!-- #principal -->
    <section id="principal" class="ds">
        <p id="fil_ariane"><a href="<?php echo url_for('ds') ?>">Page d'accueil</a> &gt; <strong>Stocks : consultation & déclaration</strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
			<h2>Résumé de la DS</h2>
			<?php
			   include_partial('dsInformations', array('ds' => $ds));
			?>
                        <h2>Contenu de la DS</h2>
                        <?php include_partial('document_validation/validation', array('validation' => $validation)); ?>
			<?php 
			include_partial('dsRecapitulatif', array('ds' => $ds, 'declarations' => $ds->declarations, 'validation' => $validation));
			?>
		</section>
    </section>
    <!-- fin #principal -->


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

