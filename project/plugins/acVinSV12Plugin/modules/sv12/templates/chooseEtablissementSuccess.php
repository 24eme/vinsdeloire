
    <!-- #principal -->
    <section id="principal">
        <p id="fil_ariane"><strong>Page d'accueil</strong></p>
        
        <!-- #contenu_etape -->
        <section id="contenu_etape">
            <?php include_component('sv12', 'chooseEtablissement'); ?>
            <?php include_partial('sv12/list', array('list' => $historySv12)) ?>
        </section>
        <!-- fin #contenu_etape -->
    </section>
    <!-- fin #principal -->
