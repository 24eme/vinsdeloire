<section id="principal" class="recherche_contact">
 <section id="contenu_etape">
   <h2>Modification des tags en cours</h2>
   <p><?php echo $restants; ?> restants</p>
 </section>
</section>
<?php
	slot('colButtons'); 
?>
 <div class="bloc_col" id="action" >
	<h2>Actions</h2>

	<div class="contenu">
		<ul>
			<li class=""><a class="btn_majeur btn_acces" href="<?php echo url_for('societe');?>">Accueil des contacts</a></li>
                </ul>
                <ul>
                        <li class=""><a class="btn_majeur btn_acces" href="<?php echo url_for('compte_search');?>">Accueil rech. avancée</a></li>
		</ul>
	</div>
</div>
<?php
	end_slot();
?>
