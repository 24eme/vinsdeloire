<section id="principal"  class="produit">
    <p id="fil_ariane"><a href="/vinsdeloire_dev.php/produits">Page d'accueil</a> &gt; <strong><?php echo sprintf("Modification du noeud %s: %s (%s)", $form->getObject()->getTypeNoeud(), $form->getObject()->getLibelle(), $form->getObject()->getKey()) ?></strong></p>
    <h2>Modification du noeud</h2>
    <div class="form_contenu">
    <?php include_partial('produit/form', array('form' => $form, 'produit' => $produit)) ?>
    </div>
</section>