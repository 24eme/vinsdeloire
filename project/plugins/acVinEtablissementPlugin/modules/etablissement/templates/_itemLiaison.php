<div class="form_ligne">
    <div class="form_colonne">
    <?php
    echo $form['type_liaison']->renderError();
    echo $form['type_liaison']->renderLabel();
    echo $form['type_liaison']->render();
    ?>        
    </div>
    <div class="form_colonne">
        <?php
    echo $form['id_etablissement']->renderError();
    echo $form['id_etablissement']->renderLabel();
    echo $form['id_etablissement']->render();
    ?>
    </div>
<a href="#" class="btn_supprimer_ligne_template" data-container="div">Supprimer</a>
</div>
<script type="text/javascript">
        $('.autocomplete').combobox();
</script>
    

