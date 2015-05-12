<div id="forms_errors" style="color: red;">
    <?php include_partial('drm_edition/itemFormErrors', array('form' => $form)) ?>
</div>

<div id="colonnes_dr">
    <?php include_partial('drm_edition/itemHeader', array('config' => $config)); ?>    
    <div id="col_saisies">
        <script type="text/javascript">
            /* Colonne avec le focus par défaut */
            var colFocusDefaut = 1;
        </script>

        <div id="col_saisies_cont">
            <?php foreach ($produits as $produit): ?>
                <?php if ($produit->hasMouvementCheck()): ?>
                    <?php
                    include_component('drm_edition', 'itemForm', array(
                        'config' => $config,
                        'detail' => $produit,
                    	'active' => ($detail && $detail->getHash() == $produit->getHash()),
                        'form' => $form));
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>