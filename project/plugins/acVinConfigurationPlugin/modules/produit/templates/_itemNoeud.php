<a title="<?php echo $noeud->getFormatLibelle() ?>" href="<?php echo url_for('produit_modification', array('noeud' => $noeud->getTypeNoeud(), 'hash' => $produit->getHashForKey())) ?>">
    <?php if($cvo && $cvo->getNoeud()->getTypeNoeud() == $noeud->getTypeNoeud()): ?>
    <strong>
    <?php endif; ?>
    <?php echo ($noeud->getLibelle()) ? $noeud->getLibelle() : sprintf("(%s)", $noeud->getKey()) ?>
    <?php if($cvo && $cvo->getNoeud()->getTypeNoeud() == $noeud->getTypeNoeud()): ?>
    </strong>
    <?php endif; ?>
</a>