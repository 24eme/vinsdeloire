<!-- #principal -->
<section id="principal">
	<p id="fil_ariane"><a href="<?php echo url_for('societe');?>">Page d'accueil</a>
            &gt; <a href="<?php echo url_for('societe_visualisation',array('identifiant'=> $societe->identifiant));?>">
            <?php echo $societe->raison_sociale; ?></a> &gt; <strong><?php echo (!$compte->isNew())? $compte->nom_a_afficher : 'Nouvel interlocuteur'; ?></strong></p>

	<!-- #contacts -->
	<section id="contacts">
		<div id="nouveau_contact">
			<h2><?php echo (!$compte->isNew())? $compte->nom_a_afficher : 'Nouvel interlocuteur'; ?></h2>


			<form action="<?php echo ($compte->isNew())? url_for('compte_ajout', array('identifiant' => $societe->identifiant)) : url_for('compte_modification', array('identifiant' => $compte->identifiant)); ?>" method="post">
			<div class="form_btn">
                <?php if($compte->isNew()): ?>
				    <a href="<?php echo url_for('societe_visualisation', $societe);?>" class="btn_majeur btn_annuler">Annuler</a>
                <?php else: ?>
                    <a href="<?php echo url_for('compte_visualisation', $compte);?>" class="btn_majeur btn_annuler">Annuler</a>
                <?php endif; ?>
				<button class="btn_majeur btn_valider">
                    <?php echo (!$compte->isSameCoordonneeThanSociete()) ? 'Valider et saisir les coordonnées' : 'Valider' ?>
                </button>
			</div>
				<div id="detail_contact" class="form_section ouvert">
					<h3>Détail de l'interlocuteur</h3>
					<div class="form_contenu">
                        <?php
                        echo $compteForm->renderHiddenFields();
                        echo $compteForm->renderGlobalErrors();
                        ?>
                                <div class="form_ligne">
                            <?php echo $compteForm['statut']->renderError(); ?>
                            <label for="statut">
                                <?php echo $compteForm['statut']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['statut']->render(); ?>
                        </div>
                    
                        <div class="form_ligne">
                            <?php echo $compteForm['civilite']->renderError(); ?>
                            <label for="civilite">
                                <?php echo $compteForm['civilite']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['civilite']->render(); ?>
                        </div>
                        <div class="form_ligne">
                            <label for="prenom">
                                <?php echo $compteForm['prenom']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['prenom']->render(); ?>
                            <?php echo $compteForm['prenom']->renderError(); ?>
                        </div>
                        <div class="form_ligne">
                            <label for="nom">
                                <?php echo $compteForm['nom']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['nom']->render(); ?>
                            <?php echo $compteForm['nom']->renderError(); ?>
                        </div>
                        <div class="form_ligne">
                            <label for="fonction">
                                <?php echo $compteForm['fonction']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['fonction']->render(); ?>
                            <?php echo $compteForm['fonction']->renderError(); ?>
                        </div>                
                        <div class="form_ligne">
                            <label for="commentaire">
                                <?php echo $compteForm['commentaire']->renderLabel(); ?>
                            </label>
                            <?php echo $compteForm['commentaire']->render(); ?>
                            <?php echo $compteForm['commentaire']->renderError(); ?>
                        </div> 
                    </div>
				</div>

                <div id="coordonnees_contact" class="form_section ouvert">
                    <h3>Coordonnées de l'interlocuteur</h3>
                    <?php include_partial('compte/modificationCoordonneeSameSocieteForm', array('form' => $compteForm)); ?>
                </div> 
                            
			<div class="form_btn">
				<?php if($compte->isNew()): ?>
                    <a href="<?php echo url_for('societe_visualisation', $societe);?>" class="btn_majeur btn_annuler">Annuler</a>
                <?php else: ?>
                    <a href="<?php echo url_for('compte_visualisation', $compte);?>" class="btn_majeur btn_annuler">Annuler</a>
                <?php endif; ?>
                <button class="btn_majeur btn_valider">
                    <?php echo (!$compte->isSameCoordonneeThanSociete()) ? 'Valider et saisir les coordonnées' : 'Valider' ?>
                </button>
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
    <?php if(!$compte->isNew()) : ?>
    <div class="contenu">
        <div class="btnRetourAccueil">
            <a href="<?php echo url_for('compte_visualisation', array('identifiant' => $compte->identifiant)); ?>" class="btn_majeur btn_acces"><span>Retour à la visualisation</span></a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
end_slot();
?> 
