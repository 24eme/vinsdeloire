<!-- #principal -->
<section id="principal" class="alerte">
    <p id="fil_ariane"><strong>Page d'accueil</strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <div id="consultation_alerte">
        	<h2>Consultation des alertes</h2>

        	<form action="<?php echo url_for('alerte'); ?>" method="GET">
        		<?php
        		echo $form->renderHiddenFields();
        		echo $form->renderGlobalErrors();
        		?>

        		<div class="bloc_form">
        			<div class="ligne_form">
        				<?php echo $form['identifiant']->renderError(); ?>
        				<?php echo $form['identifiant']->renderLabel() ?>
        				<?php echo $form['identifiant']->render() ?>
        			</div>
        			<div class="ligne_form ligne_form_alt">
        				<?php echo $form['region']->renderError(); ?>
        				<?php echo $form['region']->renderLabel() ?>
        				<?php echo $form['region']->render() ?>
        			</div>
        			<div class="ligne_form">
        				<?php echo $form['type_alerte']->renderError(); ?>
        				<?php echo $form['type_alerte']->renderLabel() ?>
        				<?php echo $form['type_alerte']->render() ?>
        			</div>
        			<div class="ligne_form ligne_form_alt">
        				<?php echo $form['statut_courant']->renderError(); ?>
        				<?php echo $form['statut_courant']->renderLabel() ?>
        				<?php echo $form['statut_courant']->render() ?>
        			</div>
        			<div class="ligne_form">
        				<?php echo $form['campagne']->renderError(); ?>
        				<?php echo $form['campagne']->renderLabel() ?>
        				<?php echo $form['campagne']->render() ?>
        			</div>
        		</div>

        		<div class="btn_form">
        			<a href="<?php echo url_for('alerte'); ?>" class="btn_majeur btn_modifier">Réinitialisation</a>
        			<button type="submit" id="alerte_search_valid" class="btn_majeur btn_valider">Rechercher</button>
        		</div>
        	</form>
        </div>

        <div id="toutes_alertes">
            	<h2>Historique des alertes</h2>

            	<?php
            	use_helper('Date');
            	$statutsWithLibelles = AlerteClient::getStatutsWithLibelles();
            	?>

            	<?php if(!count($alertesByEtablissement)): ?>
            	<div>
            		<span>
            			Aucune alerte ouverte
            		</span>
            	</div>

            	<?php else: ?>
            	<div>
            		<span>
            			<?php echo $nbResults ?> opérateurs trouvés pour <?php echo $nbAlertes; ?> alertes
            		</span>
            	</div>
            	<?php include_partial('history_alertes_pagination', array('page' => $page, 'nbPage' => $nbPage, 'consultationFilter' => $consultationFilter, )); ?>
            	<table class="table_recap table_selection">
            		<thead>
            			<tr>
            				<th>Opérateur concerné</th>
            				<th>Nombre d'alertes</th>
            			</tr>
            		</thead>
            		<tbody>
            		 <?php foreach ($alertesByEtablissement->getRawValue() as $identifiant => $alerte) : ?>
                         <tr>
            				<td><?php echo link_to($alerte['declarant_nom'],'alerte_etablissement',
                                                    array('identifiant' => $alerte['identifiant'])); ?></td>
            				<td><?php echo $alerte['nb_alertes']; ?> alertes </td>
            			</tr>
            			<?php
            			endforeach;
            			?>
            		</tbody>
            	</table>
            	<?php include_partial('history_alertes_pagination', array('page' => $page, 'nbPage' => $nbPage, 'consultationFilter' => $consultationFilter, )); ?>
            	<?php endif; ?>
    </div>

    </section>
    <!-- fin #contenu_etape -->
</section>
<!-- fin #principal -->

<?php
if (sfConfig::get('app_alertes_debug', false)):
    slot('colButtons');
    ?>
    <div id="action" class="bloc_col">
        <h2>Action</h2>
        <div class="contenu">
            <?php include_partial('alerte/choose_date', array('dateForm' => $dateForm)); ?>
        </div>
    </div>
    <?php
    end_slot();
endif;
?>
    
