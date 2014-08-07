<!-- #header -->
<header id="header">
		<div class="contenu">
			<h1 id="logo">
				<a title="Vins de Loire - Retour à l'accueil" href="<?php echo url_for('homepage') ?>">
					<img src="/images/visuels/logo_vinsdeloire_new.png" alt="" />
				</a>
			</h1>

			<div class="conteneur_nav">
				<span class="baseline">Espace des professionels du Vignoble de Val de Loire</span>		
				<?php include_component('global', 'nav'); ?>
			</div>

			<button class="btn_menu" type="button">Menu</button>
			
			<div id="actions_utilsateur">
   				<?php if ($sf_user->hasCredential('admin')) : ?>
					<a class="admin" href="<?php echo url_for('produits') ?>">Admin</a>
   				<?php endif; ?>
                <?php if($sf_user->hasCredential(Roles::TELEDECLARATION)): ?>
   				<a href="<?php echo url_for("compte_teledeclarant_modification") ?>">Mon compte</a>
                <?php endif; ?>
   				<?php if($sf_user->isUsurpationCompte()): ?>
   					<a class="deconnexion" href="<?php echo url_for('vrac_dedebrayage') ?>">Quitter</a>
   				<?php else: ?>
					<a class="deconnexion" href="<?php echo url_for('auth_logout') ?>">Déconnexion</a>
				<?php endif; ?>
			</div>
		</div>
	</header>
<!-- fin #header -->