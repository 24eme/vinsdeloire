    <!-- #principal -->
    <section id="principal"  class="drm">
        <p id="fil_ariane"><a href="<?php echo url_for('drm') ?>">Page d'accueil</a> &gt; <strong><?php echo $etablissement->nom ?></strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <?php include_component('drm', 'chooseEtablissement', array('identifiant' => $etablissement->identifiant)); ?>
            
            <fieldset id="historique_drm">
                <legend>Historique des DRMs de l'opérateur</legend>
                <nav>
                    <ul>
                        <li><a href="<?php echo url_for('drm_etablissement', array('identifiant' => $etablissement->getIdentifiant(), 'campagne' => $campagne)); ?>">Vue calendaire</a></li>
                        <li class="actif"><span>Vue stock</span></li>
                    </ul>
                </nav>
	        <?php include_component('drm', 'stocks', array('etablissement' => $etablissement, 'campagne' => $campagne, 'formCampagne' => $formCampagne, 'hamza_style' => true)); ?>
            </fieldset>

            <?php //include_partial('drm/calendrier', array('calendrier' => $calendrier)); ?>
        </section>
        <!-- fin #contenu_etape -->
        
    </section>
    <!-- fin #principal -->