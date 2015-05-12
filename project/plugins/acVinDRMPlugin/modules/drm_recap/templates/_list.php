<div id="forms_errors" style="color: red;">
    <?php include_partial('drm_recap/itemFormErrors', array('form' => $form)) ?>
</div>

<!-- C'est degelousse -->
<p style="text-align:right; padding-bottom: 4px;">
    <a href="<?php echo url_for('drm_recap_ajout_ajax', $drm_lieu) ?>" class="btn_ajouter btn_popup" data-popup-ajax="true" data-popup="#popup_ajout_detail" data-popup-config="configForm">Ajouter un produit</a>
</p>

<div id="colonnes_dr">
    <?php include_partial('drm_recap/itemHeader', array('config_lieu' => $config_lieu)); ?>    
    <div id="col_saisies">
        <script type="text/javascript">
            /* Colonne avec le focus par défaut */
            var colFocusDefaut = 1;
        </script>

        <div id="col_saisies_cont">
            <?php foreach ($produits as $produit): ?>
                <?php if ($produit->hasMouvementCheck()): ?>
                    <?php
                    include_component('drm_recap', 'itemForm', array('produit' => $produit,
                        'config_lieu' => $config_lieu,
                        'detail' => $produit,
                    	'active' => ($detail && $detail->getHash() == $form->getObject()),
                        'form' => $form));
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>