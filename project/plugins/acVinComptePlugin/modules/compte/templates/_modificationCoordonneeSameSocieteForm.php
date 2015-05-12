<div class="form_contenu">
    <div class="form_ligne">
        <?php echo $form['adresse_societe']->renderError(); ?>
        <?php echo $form['adresse_societe']->renderLabel('Même adresse que la société ?', array('class' => 'label_liste')); ?>
        <?php echo $form['adresse_societe']->render(); ?>
    </div>
</div>