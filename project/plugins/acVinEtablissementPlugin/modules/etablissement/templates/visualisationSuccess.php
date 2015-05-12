<!-- #principal -->
<section id="principal">
        <p id="fil_ariane"><a href="<?php echo url_for('societe'); ?>">Page d'accueil</a>
        &gt; <a href="<?php echo url_for('societe_visualisation', array('identifiant' => $etablissement->getSociete()->identifiant)); ?>">
            <?php echo $etablissement->getSociete()->raison_sociale; ?></a> &gt;
        <strong>
            <?php echo $etablissement->nom; ?>
        </strong>
        </p>
        <!-- #contenu_etape -->    
        <section id="contacts">
         <div id="nouveau_etablissement">
            <h2><?php echo $etablissement->nom; ?></h2>
            <div class="form_btn">
                <?php if($modification && !$reduct_rights): ?>
                <a href="<?php echo url_for('etablissement_modification',$etablissement);?>" class="btn_majeur btn_modifier">Modifier</a>    
                <a href="<?php echo url_for('compte_search', array('q' => str_replace('COMPTE-', '', $etablissement->compte))); ?>" class="btn_majeur btn_nouveau" style="float: right;">Ajouter un tag</a>
               
                    <?php endif; ?>
            </div>
            <div id="detail_etablissement" >
                    <?php include_partial('etablissement/visualisation', array('etablissement' => $etablissement,'ordre' => 0)); ?>
            </div>

            <div id="coordonnees_contact" class="form_section etablissement ouvert">
                <h3>Coordonnées de l'établissement</h3>
                <div class="form_contenu">
                    <?php include_partial('compte/coordonneesVisualisation', array('compte' => $contact)); ?>
                </div>
            </div>
         </div>
        </section>
    </section>
<?php
slot('colButtons');
?>
<div id="action" class="bloc_col">
    <h2>Action</h2>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('societe'); ?>" class="btn_majeur btn_acces"><span>Accueil des sociétés</span></a>
        </div>
    </div>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('societe_visualisation', array('identifiant' => $societe->identifiant)); ?>" class="btn_majeur btn_acces"><span>Accueil de la société</span></a>
        </div>
    </div>
</div>
<?php
end_slot();
?> 
