<?php slot('global_css_class', 'no_right_col')?>

<section id="principal"  class="produit">
    <p id="fil_ariane"><strong>Page d'accueil</strong></p>
    <a style="float:right" href="<?php echo url_for('produit_nouveau') ?>" class="btn_majeur btn_nouveau">Ajouter un produit</a>

    <h2>Produits</h2>

    <table class="table_recap table_compact">
        <thead>
            <?php include_partial('produit/itemHeader') ?>
        </thead>
        <tbody>
        <?php foreach($produits as $produit): ?>
            <?php include_component('produit', 'item', array('produit' => $produit, 'supprimable' => false)) ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

