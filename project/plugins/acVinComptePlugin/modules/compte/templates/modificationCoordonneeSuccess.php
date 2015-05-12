<!-- #principal -->
<section id="principal">
    <p id="fil_ariane"><a href="<?php echo url_for('societe');?>">Page d'accueil</a> &gt; Contacts &gt; <a href="<?php echo url_for('societe_visualisation',array('identifiant'=> $societe->identifiant));?>"> <?php echo $societe->raison_sociale; ?></a> &gt; <strong> <?php echo 'Modification établissement' ;?> </strong></p>

    <!-- #contenu_etape -->
    <section id="contacts">
        <?php if($compte->compte_type == CompteClient::TYPE_COMPTE_ETABLISSEMENT): ?>
            <div id="nouveau_etablissement" class="etablissement">
        <?php else: ?>
            <div id="nouveau_contact">
        <?php endif; ?>
            <h2><?php echo $compte->nom_a_afficher ?></h2>
                <form action="<?php echo url_for('compte_coordonnee_modification', $compte); ?>" method="post">
			<div class="form_btn">
				<a href="<?php echo url_for('compte_visualisation', $compte);?>" class="btn_majeur btn_annuler">Annuler</a>
				<button id="btn_valider" type="submit" class="btn_majeur btn_valider">Valider</button>
			</div>
                <div id="coordonnees_contact" class="form_section ouvert">
                    <?php if($compte->compte_type == CompteClient::TYPE_COMPTE_ETABLISSEMENT): ?>
                    <h3>Coordonnées de l'etablissement</h3>
                    <?php else: ?>
                    <h3>Coordonnées de l'interlocuteur</h3>
                    <?php endif; ?>
				    <?php include_partial('compte/modificationCoordonnee', array('compteForm' => $compteForm)) ?>
                </div>  
			<div class="form_btn">
				<a href="<?php echo url_for('compte_visualisation', $compte);?>" class="btn_majeur btn_annuler">Annuler</a>
				<button id="btn_valider" type="submit" class="btn_majeur btn_valider">Valider</button>
			</div>
            </form>	
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
