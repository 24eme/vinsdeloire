<?php
use_helper('Display');
?>
<script type="text/javascript">
    $(document).ready(function() 
    { 
        init_informations('mandataire');       
       <?php
        if(!isset($numero_contrat))
        {
       ?>
        ajaxifyGet('modification','#vrac_mandataire_identifiant','#mandataire_modification_btn','#mandataire_informations'); 
       <?php
        }
        else
        {
       ?>        
        ajaxifyGet('modification',{field_0 : '#vrac_mandataire_identifiant',
                                   'type' : 'mandataire' ,
                                   'numero_contrat' : '<?php echo $numero_contrat;?>'
                                  },'#mandataire_modification_btn','#mandataire_informations');           
       <?php
        }
       ?>
       removeGreyPanel('vendeur');
       removeGreyPanel('acheteur');
       removeGreyPanel('has_mandataire');
       removeGreyPanel('ligne_btn');
       removeGreyPanel('interne');
    });
</script>
<?php $asterisk = ($isTeledeclarationMode)? '' : '*'; ?>
<div class="mandataire_infos bloc_form bloc_form_condensed">
    <div class="ligne_form">
        <span>
            <label>Nom du courtier :</label>
            <?php display_field($mandataire,'nom'); ?>
        </span>
    </div>
    <div class="ligne_form ligne_form_alt">    
        <span>
            <label>N° carte professionnelle</label>
            <?php display_field($mandataire,'carte_pro'); ?>
        </span>
    </div>
    <div class="ligne_form">       
        <span>
            <label>Adresse</label>
            <?php  display_field($mandataire,'siege/adresse');  ?>
        </span>
    </div>
    <div class="ligne_form ligne_form_alt"> 
        <span>
            <label>CP<?php echo $asterisk ?></label>
            <?php  display_field($mandataire,'siege/code_postal');  ?>
        </span>
    </div>
    <div class="ligne_form ">      
        <span>
            <label>Ville<?php echo $asterisk ?></label>
            <?php  display_field($mandataire,'siege/commune');  ?>
        </span>
    </div>
</div>