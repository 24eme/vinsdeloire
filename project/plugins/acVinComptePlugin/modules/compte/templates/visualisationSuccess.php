<!-- #principal -->
<section id="principal">
	<p id="fil_ariane"><a href="<?php echo url_for('societe');?>">Page d'accueil</a>
            &gt; <a href="<?php echo url_for('societe_visualisation',array('identifiant'=> $societe->identifiant));?>">
            <?php echo $societe->raison_sociale; ?></a> &gt; 
            <strong><?php echo ($compte->nom_a_afficher)? $compte->nom_a_afficher : $compte->nom ;?></strong></p>

	<!-- #contacts -->
	<section id="contacts">
		<div id="nouveau_contact">
                    <h2><?php echo ($compte->nom_a_afficher)? $compte->nom_a_afficher : $compte->nom ;?>&nbsp;(<?php echo $compte->identifiant;?>)</h2>

			<div class="form_btn">
                            <?php if($modification || $reduct_rights): ?>
                            <a href="<?php echo url_for('compte_modification',$compte);?>" class="btn_majeur btn_modifier">Modifier</a>
                            <a href="<?php echo url_for('compte_search', array('q' => $compte->identifiant)); ?>" class="btn_majeur btn_nouveau" style="float: right;">Ajouter un tag</a>
                            <?php endif; ?>
                        </div>

				<div id="detail_contact" class="form_section contact ouvert">
					<h3>Détail de l'interlocuteur</h3>
					<?php include_partial('detailVisualisation', array('compte' => $compte)); ?>
				</div>

				<div id="coordonnees_contact" class="form_section contact ouvert">
					<h3>Coordonnées de l'interlocuteur</h3>
					<div class="form_contenu">
						<?php include_partial('coordonneesVisualisation', array('compte' => $compte)); ?>
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
