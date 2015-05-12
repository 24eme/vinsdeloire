<form action="<?php echo url_for('facture');?>" method="post">
    <div class="section_label_maj" id="recherche_operateur">
        <?php echo $form['identifiant']->renderError(); ?>
        <?php echo $form['identifiant']->renderLabel(); ?>
        <?php echo $form['identifiant']->render(); ?>

        <button id="btn_rechercher" type="submit">Rechercher</button>
    </div>
</form>