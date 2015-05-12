
    <!-- #principal -->
    <section id="principal">
        <p id="fil_ariane"><strong>Page d'accueil</strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <?php include_component('facture', 'chooseSociete'); ?>
            <?php include_partial('historiqueGeneration', array('generations' => $generations)); ?>
            <?php include_component('facture','generationMasse'); ?>
            
        </section>
        <!-- fin #contenu_etape -->
    </section>
    <!-- fin #principal -->
    