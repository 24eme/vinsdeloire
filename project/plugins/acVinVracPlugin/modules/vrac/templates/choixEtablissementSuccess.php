<?php
?>
<section id="principal">

    <h2 class="titre_societe">
        Espace de <?php echo $societe->raison_sociale; ?>
    </h2>
    <div class="clearfix">

        <div id="etablissement_<?php echo $etablissementPrincipal->identifiant; ?>" class="infos_etablissement">
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

    <form id="vrac_choix_etablissement" method="post" action="<?php echo url_for('vrac_societe_choix_etablissement', array('identifiant' => $societe->identifiant)) ?>"> 
         <?php echo $form->renderHiddenFields() ?>
         <?php echo $form->renderGlobalErrors() ?>
         <div id="condition">
            <!--  Affichage du type de contrat (si standard la suite n'est pas affiché JS)  -->
            <div id="type_contrat" class="section_label_maj">
                <?php echo $form['etablissementChoice']->renderError() ?>        
                <?php echo $form['etablissementChoice']->renderLabel() ?>
                <?php echo $form['etablissementChoice']->render() ?>
            </div>            
         </div>
        <div class="btn_etape">
                <a href="<?php echo url_for('vrac_societe', array('identifiant' => $etablissementPrincipal->identifiant)); ?>" class="btn_etape_prec"><span>retourner à l'accueil</span></a>
                <button class="btn_etape_suiv" type="submit"><span>Nouveau Contrat</span></button>
        </div>
    </form>
   

</section>

<?php

include_partial('colonne_droite', array('societe' => $societe, 'etablissementPrincipal' => $etablissementPrincipal));

?>