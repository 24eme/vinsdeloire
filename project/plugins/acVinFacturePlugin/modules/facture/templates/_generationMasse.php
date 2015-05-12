<form id="facture_generation" action="<?php echo url_for('facture_generation'); ?>" method="post">
<h2>Génération en masse</h2>
<div class="generation_facture_options">
    <ul>
        <li>
            <span>1. <?php  echo $generationForm['regions']->renderlabel(); ?></span>
            <div class="ligne_form">
            <?php echo $generationForm['regions']->renderError() ?>        
            <?php  echo $generationForm['regions']->render(); ?> 
            </div>
        </li>
        <li>
            <span>2. Séléctionner le type de document à facturer : </span>
            <div class="ligne_form">
                    <?php echo $generationForm['type_document']->renderError() ?> 
                    <?php echo $generationForm['type_document']->render(); ?>             
            </div>
        </li>
        <li>
            <span>3. Définir les seuils de facturation et d'avoir : </span>
            <div class="ligne_form">
                    <?php echo $generationForm['seuil']->renderError() ?> 
                    <?php echo $generationForm['seuil']->render(); ?>             
            </div>
        </li>
        <li>
            <span>4. Choisir la date de facturation :</span>
            <span>(Tous les mouvements antérieurs à la date saisie seront facturés. Cette date figurera sur la facture)</span>
            <div class="ligne_form champ_datepicker">
                <?php  echo $generationForm['date_facturation']->renderlabel(); ?>
                <?php echo $generationForm['date_facturation']->renderError() ?> 
                <?php  echo $generationForm['date_facturation']->render(); ?>
            </div>
            <div class="ligne_form champ_datepicker">
                <?php  echo $generationForm['date_mouvement']->renderlabel(); ?>
                <?php echo $generationForm['date_mouvement']->renderError() ?> 
                <?php  echo $generationForm['date_mouvement']->render(); ?>
            </div>
            <div class="ligne_form message_communication">
                <?php  echo $generationForm['message_communication']->renderlabel(); ?>
                <?php echo $generationForm['message_communication']->renderError() ?> 
                <?php  echo $generationForm['message_communication']->render(); ?>
            </div>
        </li>
    </ul>    
</div>
</form>
<div class="generation_facture_valid">
       <span>Cliquer sur "Générer" pour lancer la génération des factures</span>
    <a href="#" id="facture_generation_btn" class="btn_majeur btn_refraichir">Générer</a>
</div>

<script type="text/javascript">
    
    $(document).ready( function()
	{
            $('#facture_generation_btn').bind('click', function()
            {
                $('form#facture_generation').submit();
		return false;
            });
        });
    
</script>

