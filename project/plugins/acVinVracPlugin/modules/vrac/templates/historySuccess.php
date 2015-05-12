<?php
use_helper('Vrac');
use_helper('Float');
?>
<section id="principal">
    <h2 class="titre_societe">
        Espace de <?php echo $societe->raison_sociale; ?>
    </h2>
    <div class="clearfix">
        <div id="etablissement_<?php echo $societe->identifiant; ?>" class="infos_etablissement">
            <div id="num_etb">
                <span>N° :</span> <?php echo $societe->identifiant; ?>
            </div>
            <div id="cp_etb">
                <span>Code postal :</span> <?php echo $societe->siege->code_postal; ?>
            </div>
            <div id="commune_etb">
                <span>Commune :</span> <?php echo $societe->siege->commune; ?>
            </div>
        </div>
    </div>
        
   <form class="filtres_historique" id="filtres_historique" action="<?php echo url_for('vrac_history',array('identifiant' => $identifiant)); ?>" method="POST">
        <?php
		  echo $form->renderHiddenFields();
		  echo $form->renderGlobalErrors();
		?>

        <div class="campagne">
            <?php echo $form['campagne']->renderError(); ?>
			<?php echo $form['campagne']->renderLabel() ?>
			<?php echo $form['campagne']->render() ?>
        </div>
        <div class="etablissement">       
			<?php echo $form['etablissement']->renderError(); ?>
			<?php echo $form['etablissement']->renderLabel() ?>
			<?php echo $form['etablissement']->render() ?> 
        </div>
    </form>

<div class="ligne_btn txt_droite">   
    <a class="btn_majeur" href="<?php echo url_for('vrac_history', array('identifiant' => $etablissementPrincipal->identifiant, 'campagne' => 'all', 'etablissement' => 'all')); ?>">
        Voir tout l'historique
    </a>
    
    <a class="btn_majeur btn_vert btn_excel" href="<?php echo url_for('vrac_history_exportCsv', array('identifiant' => $etablissementPrincipal->identifiant, 'campagne' => $campagne)); ?>">
        Télécharger Export CSV
    </a>
</div>
   <?php 
      include_partial('contratsTable', array('contrats' => $contratsByEtablissementsAndCampagne, 'societe' => $societe)); ?>    
   
</section>

<?php

include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal, 'retour' => true));

?>