<form id="generation_operateur_form" action="<?php echo url_for('ds_etablissement',$etablissement); ?>" method="post">
<h2>Génération de formulaires de déclaration</h2>
<div class="generation_facture_options">
	<div class="ligne_form champ_datepicker">
		<?php  echo $generationOperateurForm['date_declaration']->renderlabel(); ?>       
		<?php echo $generationOperateurForm['date_declaration']->renderError() ?>  
		<?php  echo $generationOperateurForm['date_declaration']->render(); ?>
	</div>   
</div>
<div class="generation_ds_operateur">
    <span>Cliquer sur "Générer" pour lancer la création du formulaire</span>
    <button type="submit" class="btn_majeur btn_refraichir">Générer</button>
</div>
</form>


<script type="text/javascript">
    
    $(document).ready( function()
	{
            $('#generation_operateur').bind('click', function()
            {
                $('form#generation_operateur_form').submit();
		return false;
            });
        });
    
</script>

