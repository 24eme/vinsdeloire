
    <!-- #principal -->
    <section id="principal" class="drm">
        <p id="fil_ariane"><a href="<?php echo url_for('drm') ?>">Page d'accueil</a> &gt; <strong><?php echo $etablissement->nom ?></strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <?php include_component('drm', 'chooseEtablissement', array('identifiant' => $etablissement->identifiant)); ?>
            
            <h2>Une DRM est déjà en cours de saisie.</h2>
            <p >Vous devez en finir la saisie avant d'en créer une nouvelle.</p>
            <a href="<?php echo url_for('drm_etablissement', $etablissement) ?>" class="btn_majeur btn_jaune">Revenir au calendrier</a>
       
          
        </section>
        <!-- fin #contenu_etape -->
        
    </section>
