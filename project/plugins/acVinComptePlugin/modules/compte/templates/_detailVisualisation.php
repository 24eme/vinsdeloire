<div class="form_contenu">
                <div class="form_ligne">
			<label for="statut">
				Statut :
			</label>
			<?php echo $compte->statut; ?>
		</div>
		<div class="form_ligne">
			<label for="civilite">
				Civilité :
			</label>
			<?php echo $compte->civilite; ?>
		</div>
		<div class="form_ligne">
			<label for="prenom">
				Prénom :
			</label>
			<?php echo $compte->prenom; ?>
		</div>
		<div class="form_ligne">
			<label for="nom">
				Nom :
			</label>
			<?php echo  $compte->nom ; ?>
		</div>
		<div class="form_ligne">
			<label for="fonction">
				Fonction :
			</label>
			<?php echo $compte->fonction; ?>
		</div>     
             <?php if ($compte->getDroits()) : ?>
                    <div class="form_ligne">
                        <label for="droits" class="label_liste">
                            Droits de l'interlocuteur : 
                        </label>
                        <ul>                            
                        <?php foreach ($compte->getDroits() as $droit) : ?>
                            <li>
                            <?php echo $droit; ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
		<div class="form_ligne">
			<label for="commentaire">
				Commentaire :
			</label>
			<pre class="commentaire"><?php echo $compte->commentaire; ?></pre>
		</div> 
</div>
