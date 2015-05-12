<div id="recherche_operateur" class="section_label_maj">
    <?php echo $form['identifiant']->renderError(); ?>
    <?php echo $form['identifiant']->renderLabel(); ?>
    <form method="post" action="<?php echo url_for('vrac'); ?>">
        <?php echo $form['identifiant']->render(); ?>
        <button type="submit" id="btn_rechercher">Rechercher</button>
    </form>
    <!--<span id="recherche_avancee"><a href="">> Recherche avancée</a></span>-->
</div>