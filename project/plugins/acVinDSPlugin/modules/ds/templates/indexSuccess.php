<!-- #principal -->
<section id="principal" class="ds">
    <p id="fil_ariane"><strong>Page d'accueil</strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <?php include_component('ds', 'chooseEtablissement'); ?>
        <?php include_partial('historiqueDsGeneration', array('generations' => $generations)); ?>


        <?php include_partial('generation', array('generationForm' => $generationForm, 'type' => 'ds')); ?>
    </section>
    <!-- fin #contenu_etape -->
</section>
<!-- fin #principal -->

